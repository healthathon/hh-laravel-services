<?php

namespace App\Http\Controllers\Api;

use App\Constants;
use App\Exceptions\AgeRestrictionException;
use App\Exceptions\CategoryNotFoundException;
use App\Exceptions\GlobalException;
use App\Exceptions\RegimenNotFoundException;
use App\Exceptions\RestrictionLevelException;
use App\Exceptions\TaskAlreadyDoneException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserNotRegisteredForTask;
use App\Helpers;
use App\Http\Controllers\Controller;
use App\Model\ShortHealthAssessment;
use App\Respositories\UserRepository;
use App\Services\Interfaces\ITaskService;
use App\Services\TaskServices;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TaskControllerV2 extends Controller
{
    private $taskServiceObject, $userRepoObject, $mixedBagController;

    public function __construct()
    {
        $this->taskServiceObject = new TaskServices();
        $this->userRepoObject = new UserRepository();
        $this->mixedBagController = new MixedBagController();
    }

    public function getUserTasksCount($userId)
    {
        try {
            $user = $this->userRepoObject->getUser($userId);
            return $this->taskServiceObject->getUserTasksCount($user);
        } catch (UserNotFoundException $e) {
            return $e->sendUserNotFoundExceptionResponse();
        }
    }

    public function getRecommendedTask($userId, $category)
    {
        try {
            $user = $this->userRepoObject->getUser($userId);
            $this->isAgeEligibilityCriteriaPass($user->birthday);
            if ($this->isMentalLevelInterventionState($category, $user->taskInformation->mental_level) || $this->isHospitalizationIssuePending($user)) {
                return Helpers::getResponse(400, Constants::NO_RECOMMENDATION_ADVISE_MESSAGE);
            } else {
                $recommendedTasks = $this->taskServiceObject->getRecommendedTask($userId, $category);
                return response()->json([
                    "statusCode" => 200,
                    "statusMessage" => "Recommended Tasks",
                    "response" => $recommendedTasks
                ])->withHeaders([
                    "Content-Type" => "application/json"
                ]);
            }
        } catch (UserNotFoundException $e) {
            return $e->sendUserNotFoundExceptionResponse();
        } catch (RestrictionLevelException $e) {
            return $e->sendRestrictionLevelException();
        } catch (CategoryNotFoundException $e) {
            return $e->sendCategoryNotFoundExceptionResponse();
        } catch (AgeRestrictionException $exception) {
            return $exception->ageRestrictionMessageResponse();
        } catch (\Exception $e) {
            Log::error("Recommendation Exception thrown" . $e->getTraceAsString());
            return Helpers::getResponse(500, "Server Error", $e->getMessage());
        }
    }

    /**
     * @param string $birthDate
     * @throws AgeRestrictionException
     */
    private function isAgeEligibilityCriteriaPass(string $birthDate)
    {
        $age = Carbon::parse($birthDate)->age;
        if ($age < 18 || $age > 60)
            throw new AgeRestrictionException();
    }

    private function isMentalLevelInterventionState(string $category, int $mentalLevel)
    {
        return (strtolower($category) === "mental" && $mentalLevel == 1);
    }

    private function isHospitalizationIssuePending($user)
    {
        $shaObject = ShortHealthAssessment::where("question", "like", "%hospitalisation due")->first();
        $yesAnswerId = $shaObject->answers->where("answer", ucfirst("yes"))->first()->id;
        $shaAnswerGivenByUserCollection = array_column($user->getUserHealthHistory->toArray(), "answer_id");
        return in_array($yesAnswerId, $shaAnswerGivenByUserCollection) ? true : false;
    }

    public function getPopularTasks($userId, $category)
    {
        try {
            $user = $this->userRepoObject->getUser($userId);
            $this->isAgeEligibilityCriteriaPass($user->birthday);
            if ($this->isMentalLevelInterventionState($category, $user->taskInformation->mental_level) || $this->isHospitalizationIssuePending($user)) {
                return Helpers::getResponse(400, Constants::NO_RECOMMENDATION_ADVISE_MESSAGE);
            } else {
                $popularTasks = $this->taskServiceObject->getPopularTask($user, $category);
                return Helpers::getResponse(200, "Popular Tasks", $popularTasks);
            }
        } catch (UserNotFoundException $e) {
            return $e->sendUserNotFoundExceptionResponse();
        } catch (AgeRestrictionException $exception) {
            return $exception->ageRestrictionMessageResponse();
        } catch (\Exception $e) {
            return Helpers::getResponse(500, "Server Error", $e->getMessage());
        }
    }

    public function dailyTaskCompleted(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'userId' => 'required',
            'taskId' => 'required',
            'isMixedBag' => 'required'
        ]);
        if ($validate->fails())
            return Helpers::getResponse(400, "Validation Error", $validate->getMessageBag()->first());
        $taskId = $request->get('taskId');
        $userId = $request->get('userId');
        $isMixedBag = $request->get('isMixedBag');
        try {
            if ($isMixedBag)
                return $this->mixedBagController->taskComplete($request);
            else {
                $responseArr = $this->taskServiceObject->dailyTaskDone($taskId, $userId, $isMixedBag);
                $taskCompleteMessage = $this->getTaskDoneMessageFromFile();
                return Helpers::getResponse(200, $taskCompleteMessage, $responseArr);
            }
        } catch (GlobalException $e) {
            return $e->sendNotEligibleException();
        } catch (UserNotFoundException $e) {
            return $e->sendUserNotFoundExceptionResponse();
        } catch (UserNotRegisteredForTask $e) {
            return $e->sendUserNotRegisteredForTaskExceptionResponse();
        } catch (TaskAlreadyDoneException $e) {
            return $e->sendTaskAlreadyDoneException();
        } catch (\Exception $e) {
            return Helpers::getResponse("500", "Server Error", $e->getMessage());
        }
    }

    private function getTaskDoneMessageFromFile()
    {
        try {
            return Storage::disk("rootDir")->get("task-complete-message.txt");
        } catch (FileNotFoundException $e) {
            Log::error("Task Complete Message File Not Exists Exception  " . $e->getMessage());
            return "Task Completed";
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribeTask(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'userId' => 'required',
            'taskId' => 'required',
            'isMixedBag' => 'required'
        ]);
        if ($validate->fails())
            return Helpers::getResponse(400, "Validation Error", $validate->getMessageBag());
        try {
            $register = $this->taskServiceObject->registerTask($request->userId, $request->taskId, $request->isMixedBag);
            return Helpers::getResponse(200, $register);
        } catch (UserNotFoundException $e) {
            return $e->sendUserNotFoundExceptionResponse();
        } catch (\Exception $e) {
            return Helpers::getResponse(500, "Internal Server Error", $e->getMessage());
        }
    }

    //TODO: Remove this function in future

    public function unsubscribeTask(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'userId' => 'required',
            'taskId' => 'required',
            'isMixedBag' => 'required'
        ]);
        if ($validate->fails())
            return Helpers::getResponse(400, "Validation Error", $validate->getMessageBag());
        try {
            $unsubscribeFlag = $this->taskServiceObject->unregisterTask($request->userId, $request->taskId, $request->isMixedBag);
            if ($unsubscribeFlag)
                return Helpers::getResponse(200, Constants::TASK_SUCCESS_UNREGISTER);
            return Helpers::getResponse(404, Constants::TASK_NOT_REGISTERED);
        } catch (UserNotFoundException $e) {
            return $e->sendUserNotFoundExceptionResponse();
        } catch (\Exception $e) {
            return Helpers::getResponse(500, "Server Error", $e->getMessage());
        }
    }

    public function displayDailyBadgeImage($taskBankId, $weekNo, $day)
    {
        try {
            return $this->taskServiceObject->loadTaskSpecificWeekDayBadge($taskBankId, $weekNo, $day);
        } catch (RegimenNotFoundException $e) {
            Log::warning($e->sendRegimenNotFoundExceptionResponse());
            return null;
        }
    }
}
