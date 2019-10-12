<?php

namespace App\Services;

use App\Constants;
use App\Events\UpdateUserState;
use App\Exceptions\AlreadyAnsweredException;
use App\Exceptions\UserNotFoundException;
use App\Helpers;
use App\Model\Assess\assesHistory;
use App\Model\Assess\Query;
use App\Model\Assess\queryTag;
use App\Model\AssessmentAnswers;
use App\Model\LongAssessUserLevelRestriction;
use App\Model\TaskCorrespondingAssessmentAnswers;
use App\Model\Tasks\taskBank;
use App\Model\User;
use App\Respositories\AssessmentRepository;
use App\Services\Interfaces\IAssessmentService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AssessmentService implements IAssessmentService
{

    private $assessmentRepo, $labService, $questionService, $nutritionScoreBankService;

    public function __construct()
    {
        $this->assessmentRepo = new AssessmentRepository();
        $this->labService = new LabService();
        $this->questionService = new QuestionService();
        $this->nutritionScoreBankService = new NutritionScoreBankService();
    }

    public function fetchAllAssessAnswers()
    {
        $assessmentAnswers = $this->assessmentRepo->getAllAssessAnswers();
        $customizeDataArr = [];
        foreach ($assessmentAnswers as $answer) {
            $regimenIds = array_column($answer->recommend_regimen->toArray(), 'recommended_regimen');
            $regimenNames = null;
            if (count($regimenIds) > 0) {
                $regimenNames = $this->mapTaskNameWithIds($regimenIds);
                $regimenNames = count($regimenNames) > 0 ? implode(",", $regimenNames) : null;
            }
            $customizeDataArr[] = [
                'id' => $answer->id,
                'query' => $answer->queryInfo->query,
                'query_id' => $answer->queryInfo->id,
                'restricted_level' => $answer->restricted_level,
                'recommended_regimen' => $regimenNames,
                'answer' => $answer->answer,
            ];
        }
        return $customizeDataArr;
    }

    public function mapTaskNameWithIds($commaSeparatedTestId)
    {
        $regimenNameArr = taskBank::whereIn('id', $commaSeparatedTestId)->pluck('task_name');
        return array_unique($regimenNameArr->toArray());
    }

    public function mapTestNameWithTestIds($commaSeparatedTestId)
    {
        $testNameArr = $this->labService->getMultipleTestNameFromTestIds($commaSeparatedTestId)->toArray();
        $testNameArr = count($testNameArr) > 0 ? array_column($testNameArr, "test_name") : [];
        return array_unique($testNameArr);
    }

    public function getTagAssessmentQuestions($tagId, $orderSeqId)
    {
        $tagName = queryTag::getTagNameFromCache($tagId);
        $setOfQuestions = Query::getRequestedTagIdQuestions($tagId);
        $finalResponseToSent = [
            'tag' => $tagName === "Emotional Well Being" ? "Mental Well-Being" : $tagName,
            'order_seq' => $orderSeqId,
            'questions' => $setOfQuestions
        ];
        return $finalResponseToSent;
    }

    /**
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\UserNotFoundException
     */
    public function getUserAssessResults($userId)
    {
        User::getUser($userId);        // will throw an exception if user not found
        $tagsIdArray = array();
        $userHistory = assesHistory::where('user_id', $userId)->first();
        if (!$userHistory)
            return Helpers::getResponse(404, "No Assessment Result");
        $userHistory = $userHistory->toArray();
        if ($userHistory['finish_state'] == 0) {
            $tmpArray['ChangeZone'] = null;
            $tmpArray['MaintainZone'] = null;
            $tmpArray['ImproveZone'] = null;
            return Helpers::getResponse(200, "Incomplete Assessment", $tmpArray);
        }
        $searchWord = "score";
        $pattern = "/" . $searchWord . "/";
        foreach ($userHistory as $key => $value) {
            if (preg_match($pattern, $key)) {
                $splitValues = explode("_", $key, '2');
                array_push($tagsIdArray, (int)substr($splitValues[0], -1));
            }
        }
        $assessmentResults = $this->calculateUserTagScoreInPercentage($userHistory, $tagsIdArray);
        return Helpers::getResponse(200, "Assessment Result", $assessmentResults);
    }

    private function calculateUserTagScoreInPercentage($userHistory, $tagsIdArray)
    {
        $tmpArray = array();
        foreach ($tagsIdArray as $tagId) {
            $scoreColumn = "tag" . $tagId . "_score";
            $tagStateColumn = "tag" . $tagId . "_state";
            $queryInfo = queryTag::where('id', $tagId)->first();
            $score = $userHistory[$scoreColumn];
            // HardCoded [Emotional Well Being - make thing dynamic from DB] - Please Resolve this bad practices. (MJ)
            $tagName = ucwords(strtolower($queryInfo->tag_name === "Emotional Well Being" ? "Mental Well-Being" : $queryInfo->tag_name));
            if ($queryInfo->overallScore === null)
                $maxScore = $queryInfo->excellent_marks != null ? $queryInfo->excellent_marks : $queryInfo->good_marks;
            else
                $maxScore = $queryInfo->overallScore->score;
            switch (strtolower($userHistory[$tagStateColumn])) {
                case "bad":
                    $tmpArray["ImproveZone"][$tagName] = round(($score / $maxScore) * 100) . "%";
                    break;
                case "good":
                    $tmpArray["ChangeZone"][$tagName] = round(($score / $maxScore) * 100) . "%";
                    break;
                case "excellent":
                    $tmpArray["MaintainZone"][$tagName] = round(($score / $maxScore) * 100) . "%";
                    break;
                default:
                    break;
            }
        }
        if (empty($tmpArray['ChangeZone']))
            $tmpArray['ChangeZone'] = null;
        if (empty($tmpArray['ImproveZone']))
            $tmpArray['ImproveZone'] = null;
        if (empty($tmpArray['MaintainZone']))
            $tmpArray['MaintainZone'] = null;
        return $tmpArray;
    }

    public function getTagState($tagId, $score)
    {
        $tagInfo = queryTag::where('id', $tagId)->first();
        if ($tagInfo->excellent_marks != null) {
            if ($score == $tagInfo->excellent_marks) {
                return Constants::EXCELLENT;
            } else if ($score < $tagInfo->excellent_marks && $score >= $tagInfo->good_marks) {
                return Constants::GOOD;
            } else {
                return Constants::BAD;
            }
        } else {
            if ($score > $tagInfo->bad_marks) {
                return Constants::GOOD;
            } else {
                return Constants::BAD;
            }
        }
    }

    /**
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     * @throws UserNotFoundException
     * @throws \Exception
     */
    public function resetUserAssessResults($userId)
    {
        $user = User::getUser($userId, ['id', 'first_name']);
        if ($user->assessmentRecord == null)
            return Helpers::getResponse(404, Constants::ASSESSMENT_NOT_YET_STARTED);
        try {
            $fileWriteStatus = $this->writeUserAssessHistoryToAmazonS3Bucket($user, $user->assessmentRecord->hasManyUserAssessmentAnswers);
            if ($fileWriteStatus) {
                DB::beginTransaction();
                $this->keepTaskWhichUserIsDoingAndDeleteOtherTask($user);
                $this->resetUserAssessDataApartFromHistoryScore($user->assessmentRecord);
                if ($user->recommendedTest()->count() > 0)
                    $user->recommendedTest()->delete();
                DB::commit();
                return Helpers::getResponse(200, Constants::RESET_ASSESSMENT);
            } else {
                return Helpers::getResponse(500, Constants::AMAZON_FILE_WRITING_FAILED);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw  new \Exception();
        }
    }

    private function writeUserAssessHistoryToAmazonS3Bucket($user, $data)
    {
        try {
            $date = new \DateTime("now");
            $fileName = $date->format("dmyhi");
            $folder = $user->first_name . "_" . $user->id . "/$fileName.json";
            Storage::disk('s3')->put($folder, $data);
            return true;
        } catch (\Exception $e) {
            Log::error("Exception while writing an Assessment History to Assessment $e");
            return false;
        }
    }

    private function keepTaskWhichUserIsDoingAndDeleteOtherTask($user)
    {
        $userDoingTask = array_column($user->doingTask->toArray(), 'regimen_id');
        $recommendedIds = array_column($user->recommendedTask->toArray(), 'regimen_id');
        $idsToRemove = array_diff($recommendedIds, $userDoingTask);
        if (count($idsToRemove) > 0)
            $user->recommendedTask()->whereIn('regimen_id', $idsToRemove)->delete();
    }

    // Delete User Entry from table on reset demand

    private function resetUserAssessDataApartFromHistoryScore($assessmentRecord)
    {
        $allTags = queryTag::all();
        $historyTagId = queryTag::where("tag_name", ucfirst("history"))->first()->id;
        $assessmentRecord->tags_completed = [$historyTagId];
        foreach ($allTags as $tag) {
            // HardCoded - Bad Coding
            if ($tag->tag_name !== "History" && $tag->tag_name !== "BMI") {
                $tagColumn = "tag" . $tag->id . "_score";
                $tagState = "tag" . $tag->id . "_state";
                $assessmentRecord->$tagColumn = 0;
                $assessmentRecord->$tagState = "Buckle Up";
            }
        }
        $assessmentRecord->finish_state = 0;
        $assessmentRecord->category1_state = -1;
        $assessmentRecord->category2_state = -1;
        $assessmentRecord->category3_state = -1;
        $assessmentRecord->save() && $assessmentRecord->hasManyUserAssessmentAnswers()->delete();
    }

    /**
     * @param $userId
     * @param $tagId
     * @param $answers : Answers provided by user
     * @param $orderSeq : Sequence of tag Id in which questions need to be ask to user
     * @throws AlreadyAnsweredException
     * @throws UserNotFoundException
     * @throws \Exception
     */
    public function recordUserTagQuestionsAnswers($userId, $tagId, $answers, $orderSeq)
    {
        $user = User::getUser($userId);
        if (!empty($user->assessmentRecord))
            $this->hasUserAlreadyAnsweredThisTagQuestion($tagId, $user);
        else {
            $user->assessmentRecord = new assesHistory();
            $user->assessmentRecord->user_id = $user->id;
        }
        $tagScoreColumn = "tag" . $tagId . "_score";
        $collectedAnswers = [];
        $restrictionLevelCollector = [];
        $newRecommendTestIds = [];
        $newRecommendTaskIds = [];
        foreach ($answers as $answer) {
            //Collecting user answers
            $collectedAnswers[] = [
                'user_assess_id' => '',
                'tag_id' => $tagId,
                'query_id' => $answer['query_id'],
                'answer' => $answer['answer_text'],
                'score' => $answer['score']
            ];
            // Very Costly Query Operation  (Handle it)
            $restrictedLevel = AssessmentAnswers::where("answer", trim($answer["answer_text"]))
                ->where("query_id", $answer['query_id'])
                ->first()
                ->restricted_level;
            if (!is_null($restrictedLevel))
                array_push($restrictionLevelCollector, $restrictedLevel);
            if (!$this->isMentalTag($tagId) || ($this->isMentalTag($tagId) && !$this->isMentalBank($answer["query_id"]))) {
                $newTaskArr = $this->fetchRecommendedTaskIdsFromUserAnswer($user->id, $answer['query_id'], $answer['answer_text']);
                if (count($newTaskArr) > 0) {
                    $newRecommendTaskIds = array_merge($newRecommendTaskIds, array_column($newTaskArr, "regimen_id"));
                }
                $newTestArr = $this->fetchRecommendedTestIdsFromUserAnswer($user->id, $answer['query_id'], $answer['answer_text']);
                if (count($newTestArr) > 0) {
                    $newRecommendTestIds = array_merge($newRecommendTestIds, array_column($newTestArr, "test_id"));
                }
            }
            // If !mental_bank
            if (!$this->isMentalTag($tagId) || ($this->isMentalTag($tagId) && $this->isMentalBank($answer["query_id"]))) {
                $user->assessmentRecord->$tagScoreColumn += $answer['score'];
            }
        }
        if ($this->isNutritionTag($tagId)) {
            $newTaskArr = $this->nutritionScoreBankService->getRecommendedRegimenIds($user->assessmentRecord->$tagScoreColumn);
            $newRecommendTaskIds = array_merge($newRecommendTaskIds, $newTaskArr);
        }
        DB::beginTransaction();
        try {
            $tagsCompleted = $user->assessmentRecord->tags_completed;
            $tagsCompleted[] = empty($tagsCompleted) ? $tagId : $tagId;
            $user->recommededTest = $newRecommendTestIds;
            $user->assessmentRecord->order_seq_id = $orderSeq;
            $user->assessmentRecord->tags_completed = $tagsCompleted;
            if ($this->isAssessmentCompletedByUser($user->assessmentRecord)) {
                $user->assessmentRecord->finish_state = 1;
                event(new UpdateUserState($user->assessmentRecord)); // calculate tags percentage
            }
            $user->assessmentRecord->save();
            if (count($restrictionLevelCollector) > 0) {
                $userLongAssessRestriction = null;
                $newMinValue = min($restrictionLevelCollector);
                if ($user->long_assess_restriction == null) {
                    $userLongAssessRestriction = new LongAssessUserLevelRestriction();
                    $userLongAssessRestriction->restriction_level = $newMinValue;
                } else {
                    $userLongAssessRestriction = $user->long_assess_restriction;
                    if ($userLongAssessRestriction->restriction_level > $newMinValue)
                        $userLongAssessRestriction->restriction_level = $newMinValue;
                }
                $userLongAssessRestriction->user_id = $user->id;
                $userLongAssessRestriction->save();
            }
            $updatedCollection = $this->addUserAnswerToAssessmentSheet($collectedAnswers, $user->assessmentRecord->id); // record answer
            $user->assessmentRecord->hasManyUserAssessmentAnswers()->createMany($updatedCollection);
            //Insert Task Recommendation
            $recommendedTestIds = array_unique($newRecommendTestIds);
            $recommendedTaskIds = array_unique($newRecommendTaskIds);
            foreach ($recommendedTaskIds as $key => $recommendedTaskId) {
                $user->recommendedTask()->updateOrCreate([
                    'user_id' => $user->id,
                    'regimen_id' => $recommendedTaskId
                ], [
                    'user_id' => $user->id,
                    'regimen_id' => $recommendedTaskId
                ]);
            }
//            Insert Test Recommendation
            foreach ($recommendedTestIds as $recommendedTestId) {
                $user->recommendedTest()->updateOrCreate([
                    'user_id' => $user->id,
                    'test_id' => $recommendedTestId
                ], [
                    'user_id' => $user->id,
                    'test_id' => $recommendedTestId
                ]);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /**
     * @param $tag_id
     * @param $user
     * @throws AlreadyAnsweredException
     */
    private function hasUserAlreadyAnsweredThisTagQuestion($tag_id, $user)
    {
        $tagsCompleted = $user->assessmentRecord->tags_completed;
        if (!empty($tagsCompleted)) {
            $isAnswered = in_array($tag_id, $tagsCompleted);
            if ($isAnswered)
                throw new AlreadyAnsweredException();
        }
    }

    private function isMentalTag(int $tagId)
    {
        // TODO: HardCoded - Wrong Practice
        return queryTag::getTagName($tagId) === "Mental Well-Being";
    }

    private function isMentalBank(int $queryId)
    {
        $queries = Query::all("id", "is_mental_bank");
        $data = [];
        if (Cache::has("queries_mental_bank")) {
            $data = Cache::get("queries_mental_bank");
        } else {
            $data = Cache::rememberForever("queries_mental_bank", function () use ($queries, $data) {
                foreach ($queries as $query) {
                    $data[$query->id] = $query->is_mental_bank;
                }
                return $data;
            });
        }
        return $data[$queryId];
    }

    private function fetchRecommendedTaskIdsFromUserAnswer($userId, $queryId, $answer)
    {
        $response = [];
        $queryObject = AssessmentAnswers::where('query_id', $queryId)
            ->where('answer', trim($answer))->first();
        $recommendedTaskIds = [];
        if ($queryObject->recommend_regimen !== null)
            $recommendedTaskIds = array_column($queryObject->recommend_regimen->toArray(), 'recommended_regimen');
        if (count($recommendedTaskIds) > 0) {
            foreach ($recommendedTaskIds as $taskId) {
                if ($taskId != 0) {
                    $response[] = [
                        'user_id' => $userId,
                        'regimen_id' => $taskId
                    ];
                }
            }
        }
        return $response;
    }

    private function fetchRecommendedTestIdsFromUserAnswer($userId, $queryId, $answer)
    {
        $response = [];
        $queryObject = AssessmentAnswers::where('query_id', $queryId)
            ->where('answer', trim($answer))->first();
        $recommendedTestIds = [];
        if ($queryObject->recommend_test !== null)
            $recommendedTestIds = array_column($queryObject->recommend_test->toArray(), 'recommended_test');
        if (count($recommendedTestIds) > 0) {
            foreach ($recommendedTestIds as $taskId) {
                if ($taskId != 0) {
                    $response[] = [
                        'user_id' => $userId,
                        'test_id' => $taskId
                    ];
                }
            }
        }
        return $response;
    }

    private function isNutritionTag(int $tagId)
    {
        // TODO: HardCoded - Wrong Practice
        return queryTag::getTagName($tagId) === "Nutrition";
    }

    private function isAssessmentCompletedByUser($userAssessObject)
    {
        $completedTagId = $userAssessObject->tags_completed;
        $totalQuestionTags = queryTag::where("tag_name", "<>", "BMI")->get()->count();
        return count($completedTagId) == ($totalQuestionTags);
    }

    private function addUserAnswerToAssessmentSheet($answersCollection, $userAssessObjectId)
    {
        array_walk($answersCollection, function (&$key) use ($userAssessObjectId) {
            $key["user_assess_id"] = $userAssessObjectId;
        });
        return $answersCollection;
    }

    public function updateQueryAnswersRegimen(string $answer, int $queryId, array $regimenIds)
    {
        $query = $this->questionService->fetchQuestionById($queryId, ['id', 'tag_id', 'query', 'results_string']);
        $answerObj = $query->answers()->where('answer', $answer)->first();
        $answerId = $answerObj->id;
        $newRecommendedRegimen = [];
        foreach ($regimenIds as $regimenId) {
            $newRecommendedRegimen[] = [
                'answer_id' => $answerId,
                'recommended_regimen' => $regimenId
            ];
        }
        $previousRecommendation = TaskCorrespondingAssessmentAnswers::where('answer_id', $answerId);
        if (count($previousRecommendation->get()) > 0) {
            $previousRecommendation->delete();
        }
        return $this->updateTaskRecommendation($newRecommendedRegimen);
    }

    private function updateTaskRecommendation($newRecommendedRegimen)
    {
        TaskCorrespondingAssessmentAnswers::insert($newRecommendedRegimen);
        return response()->json([
            'status' => true,
            'message' => "Updated"
        ]);
    }
}