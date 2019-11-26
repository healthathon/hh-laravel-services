<?php

namespace App\Http\Controllers\API;

use App\Exceptions\UserNotFoundException;
use App\Helpers;
use App\Http\Controllers\Controller;
use App\Model\ShortHealthAssessment;
use App\Model\User;
use App\Respositories\ShortHealthAssessmentRepository;
use App\Respositories\UserRepository;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShortHealthAssessmentController extends Controller
{

    private $userService, $ShortHealthAssessmentRepo;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->ShortHealthAssessmentRepo = new ShortHealthAssessmentRepository();
    }

    public function getQuestions()
    {
        $shortQuestions = $this->ShortHealthAssessmentRepo->with('answers:id,question_id,answer')->get();
        return Helpers::getResponse(200, "SHA Questions", $shortQuestions);
    }

    /**
     * This function first validates the received values and then pass data to user service
     * call @link UserService@putAboutUserShortHealthData
     *
     * @param Request $request HTTP Request Body
     * @param $id User ID
     * @return \Illuminate\Http\JsonResponse Status of Data Save
     */
    public function putAboutUserShortHealthHistory(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "answers" => "required"
        ]);
        if ($validator->fails()) {
            return Helpers::getResponse(400, "Validator Error", $validator->errors()->first());
        }
        return $this->userService->putAboutUserShortHealthData($id, $request->all());
    }

    public function getAboutUserShortHealthHistory($id)
    {
        try {
            $user = (new UserRepository())->getUser($id, ['id', 'first_name', 'last_name']);
            $shortQuestions = $this->ShortHealthAssessmentRepo->with('answers:id,question_id,answer')->get();
            $userSHAData = $user->getShortHealthData()->get();
            
            //return $userSHAData;

            if (count($userSHAData) > 0)
                $user["sha"] = $this->userHaveSHA($userSHAData, $shortQuestions);
            else
                $user["sha"] = $this->userNotHaveSHA($shortQuestions);
            return Helpers::getResponse(200, "User Health Data", $user);
        } catch (UserNotFoundException $e) {
            $e->setMessage("User not found");
            return Helpers::getResponse(404, $e->getMessage());
        }
    }

    private function userHaveSHA($userSHAData, $shortQuestions)
    {
        $answersCollected = [];
        foreach ($userSHAData as $value) {
            $header = $value->belongsToQuestion["header"];
            $answersCollected[$header][] = $value->belongsToAnswers["answer"];
        }
        foreach ($shortQuestions as $question) {
            $header = $question["header"];
            $answersOfHeader = array_key_exists($header, $answersCollected) ? $answersCollected[$header] : [];
            foreach ($question->answers as $answer) {
                if (count($answersOfHeader) > 0 && in_array($answer["answer"], $answersOfHeader))
                    $answer["isSelected"] = 1;
                else
                    $answer["isSelected"] = 0;
            }
        }
        return $shortQuestions;
    }

    private function userNotHaveSHA(object $shortQuestions)
    {
        foreach ($shortQuestions as $question) {
            foreach ($question->answers as $answer) {
                $answer["isSelected"] = 0;
            }
        }
        return $shortQuestions;
    }
}
