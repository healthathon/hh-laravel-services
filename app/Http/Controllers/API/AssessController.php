<?php

namespace App\Http\Controllers\API;

use App\Exceptions\AlreadyAnsweredException;
use App\Exceptions\UserNotFoundException;
use App\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Assess\AssessmentQuestionsTagOrder;
use App\Model\User;
use App\Services\AssessmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AssessController extends Controller
{
    private $assessmentService;

    public function __construct()
    {
        $this->assessmentService = new AssessmentService();
    }

    // show user individual tag score in percentage
    public function getUserAssessResults($userId)
    {
        try {
            return $this->assessmentService->getUserAssessResults($userId);
        } catch (UserNotFoundException $e) {
            $e->setMessage("User not found");
            return $e->sendUserNotFoundExceptionResponse();
        }
    }

    public function getAssessmentQuestionsForUser($userId)
    {
        try {
            $user = User::getUser($userId);
            if ($user->assessmentRecord != null) {
                if ($user->assessmentRecord->finish_state) {
                    $totalScore = $user->assessmentRecord->hasManyUserAssessmentAnswers->sum('score');
                    return Helpers::getResponse(200, "Your score is $totalScore");
                } else {
                    // Sequence of Questions Tag Order user is following
                    $orderSeqId = $user->assessmentRecord->order_seq_id;
                    $orderSeq = AssessmentQuestionsTagOrder::getRequestedIdOrderSequence($orderSeqId);
                    $remainingQuestionTagIds = array_diff(explode(",", $orderSeq->order_seq), $user->assessmentRecord->tags_completed);
                    $nextTagId = array_first($remainingQuestionTagIds);
                    $questionsResponse = $this->assessmentService->getTagAssessmentQuestions($nextTagId, $orderSeqId);
                }
            } else {
                $orderSeq = AssessmentQuestionsTagOrder::getActiveOrderSequence();
                $startTagId = explode(",", $orderSeq->order_seq)[0];
                $questionsResponse = $this->assessmentService->getTagAssessmentQuestions($startTagId, $orderSeq->id);
            }
            return Helpers::getResponse(200, "Assessment Questions", $questionsResponse);
        } catch (UserNotFoundException $e) {
            return $e->sendUserNotFoundExceptionResponse();
        }
    }

    public function resetUserAssessResults($userId)
    {
        try {
            return $this->assessmentService->resetUserAssessResults($userId);
        } catch (UserNotFoundException $e) {
            return $e->sendUserNotFoundExceptionResponse();
        } catch (\Exception $e) {
            Log::error("Exception in AssessController @resetAssessResults" . $e->getMessage());
            return Helpers::getResponse(500, "Internal Server Error");
        }
    }

    //save user answers
    public function recordUserTagQuestionsAnswers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'tag_id' => 'required',
            'order_seq' => 'required',
            'answers' => 'required'
        ], $this->validationMessageForSavingUserAnswer());
        if ($validator->fails())
            return Helpers::getResponse(400, "Validation Error", $validator->getMessageBag()->first());
        $userId = $request->get('user_id');
        $tagId = $request->get('tag_id');
        $orderSeq = $request->get('order_seq');
        $answers = $request->get('answers');
        try {
            $this->assessmentService->recordUserTagQuestionsAnswers($userId, $tagId, $answers, $orderSeq);
            return Helpers::getResponse(200, "User answers saved");
        } catch (UserNotFoundException $e) {
            $e->setMessage("User not found");
            return $e->sendUserNotFoundExceptionResponse();
        } catch (AlreadyAnsweredException $e) {
            $e->setMessage("You already answered Tag $tagId questions");
            return $e->sendAlreadyAnsweredExceptionResponse();
        } catch (\Exception $e) {
            Log::error("Exception occurred while saving assessment answers" . $e->getTraceAsString());
            return Helpers::getResponse(500, "Server Error", $e->getMessage());
        }
    }

    // Delete User Entry from table on reset demand
    private function validationMessageForSavingUserAnswer()
    {
        return [
            'user_id.required' => "Please provide user identity",
            'tag_id.required' => "Please provide tag identity",
            'order_seq.required' => "Please provide sequence number of questions",
            'answers.required' => "Please provide answers from user"
        ];
    }
}
