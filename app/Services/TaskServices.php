<?php

namespace App\Services;


use App\Constants;
use App\Exceptions\AgeRestrictionException;
use App\Exceptions\CategoryNotFoundException;
use App\Exceptions\GlobalException;
use App\Exceptions\NoRecommendationException;
use App\Exceptions\RestrictionLevelException;
use App\Exceptions\TaskAlreadyDoneException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserNotRegisteredForTask as UserNotRegisteredForTaskException;
use App\Helpers;
use App\Http\Controllers\Api\FeedsController;
use App\Model\ShortHealthAssessment;
use App\Model\Tasks\UserTaskTracker;
use App\Model\User;
use App\Model\UserAchievements;
use App\Model\UserRegimenScore;
use App\Model\UserTask;
use App\Respositories\AssessmentRepository;
use App\Respositories\CategoryRepository;
use App\Respositories\TaskBankRepository;
use App\Respositories\UserRepository;
use App\Respositories\WeeklyTaskRepository;
use App\Services\Interfaces\ITaskService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskServices implements ITaskService
{

    private $categoryRepo, $userRepo, $assessHistoryRepo, $cloudinaryService, $weeklyTaskRepo,
        $taskBankRepo, $helpers, $feedsController, $scoreCalculatorService, $mixedBagService;

    public function __construct()
    {
        $this->categoryRepo = new CategoryRepository();
        $this->userRepo = new UserRepository();
        $this->assessHistoryRepo = new AssessmentRepository();
        $this->taskBankRepo = new TaskBankRepository();
        $this->helpers = new Helpers();
        $this->feedsController = new FeedsController();
        $this->scoreCalculatorService = new ModuleScoreServices();
        $this->mixedBagService = new MixedBagService();
        $this->cloudinaryService = new CloudinaryService();
        $this->weeklyTaskRepo = new WeeklyTaskRepository();
    }

    /**
     * Get User Task Count doing in each category [ Max: 2 , Min : 0]
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserTasksCount(User $user)
    {
        $userDoingTasks = $user->doingTask()->get(['regimen_id', 'regimen_category']);
        $response = $this->getInitialCategoryTaskCount();
        if (count($userDoingTasks) === 0)
            $taskMessage = "User has not started  any task yet";
        else {
            $taskMessage = "Tasks Count";
            foreach ($userDoingTasks as $userDoingTask) {
                $categoryName = $userDoingTask->category->name;
                $arrayKey = ucfirst($categoryName) . "_count";
                if (array_key_exists($arrayKey, $response))
                    $response[$arrayKey] += 1;
            }
        }
        return Helpers::getResponse(200, $taskMessage, $response);
    }

    private function getInitialCategoryTaskCount()
    {
        return [
            Constants::PHYSICS . '_count' => 0,
            Constants::NUTRITION . '_count' => 0,
            Constants::LIFESTYLE . '_count' => 0,
            Constants::MENTAL . '_count' => 0
        ];
    }

    /**
     * @param int $userId
     * @param string $category
     * @return array|\Illuminate\Http\JsonResponse
     * @throws AgeRestrictionException
     * @throws CategoryNotFoundException
     * @throws NoRecommendationException
     * @throws RestrictionLevelException
     * @throws UserNotFoundException
     */
    public function getRecommendedTask(int $userId, string $category)
    {
        $user = null;
        $user = $this->userRepo->getUser($userId);
        if ($user->assessmentRecord == null || $user->assessmentRecord->finish_state !== 1)
            return $this->mixedBagService->getCategoryMixedBagTasks($userId, $category);
        $this->isAgeEligibilityCriteriaPass($user->birthday);
        $this->isMentalLevelInterventionState($category, $user->taskInformation->mental_level);
        $this->isHospitalizationIssuePending($user);
        $categoryId = $this->categoryRepo->getCategoryIdByName($category);
        $categoryName = strtolower($category);
        $category = $category === Constants::PHYSICAL ? "physical" : strtolower($category);
        $categoryLevelColumn = $category . "_level";
        $userCategoryLevel = $user->taskInformation->$categoryLevelColumn;
        switch ($categoryName) {
            case strtolower(Constants::PHYSICAL):
                $currentRestrictedLevel = -1;
                if ($user->long_assess_restriction != null || $user->restriction != null) {
                    if ($user->long_assess_restriction != null && $user->restriction != null)
                        $currentRestrictedLevel = min($user->long_assess_restriction->restriction_level, $user->restriction->restriction_level);
                    if ($user->long_assess_restriction == null)
                        $currentRestrictedLevel = $user->restriction->restriction_level;
                    else if ($user->restriction == null)
                        $currentRestrictedLevel = $user->long_assess_restriction->restriction_level;
                    if ($userCategoryLevel > $currentRestrictedLevel) {
                        throw new RestrictionLevelException();
                    }
                }
                $tasks = $this->taskBankRepo->getTaskByLevelAndCategory($categoryId, $userCategoryLevel);
                break;
            case strtolower(Constants::MENTAL):
                $tasks = $this->taskBankRepo->getTaskByLevelAndCategory($categoryId, $userCategoryLevel);
                break;
            case strtolower(Constants::LIFESTYLE):
            case strtolower(Constants::NUTRITION):
                $tasks = $this->taskBankRepo->getTaskBasedOnUserAssessmentAnswers($categoryId, $user);
                break;
            default:
                throw new CategoryNotFoundException();
                break;
        }
        $tasks = new Collection($tasks);
        $taskResponse = $this->getFormattedTaskResponse($user, $tasks);
        $userDoingTasksObject = $user->doingTask()->where('regimen_category', $categoryId)->get();
        $DoingTaskIds = [];
        foreach ($userDoingTasksObject as $value)
            $DoingTaskIds[] = $value->regimenInfo->id;
        $responseArr = [
            'isMixedBag' => false,
            "Doing_Tasks" => $DoingTaskIds,
            'task' => $taskResponse['taskResponse'],
            'stopToContinueNextWeekTask' => $taskResponse['stopToContinueNextWeekTask']
        ];
        return $responseArr;
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

    /**
     * @param string $category
     * @param int $mentalLevel
     * @return bool
     * @throws NoRecommendationException
     */
    private function isMentalLevelInterventionState(string $category, int $mentalLevel)
    {
        if (strtolower($category) === "mental" && $mentalLevel == 1)
            throw new NoRecommendationException();
        return false;
    }

    /**
     * @param $user
     * @return bool
     * @throws NoRecommendationException
     */
    private function isHospitalizationIssuePending($user)
    {

        $shaObject = ShortHealthAssessment::where("is_hospitalisation", 'yes')->first();

//        dd($shaObject->answers);

        if (isset($shaObject) && $shaObject->answers != null) {
            $yesAnswerId = $shaObject->answers->where("answer", ucfirst("yes"))->first()->id;

            $shaAnswerGivenByUserCollection = array_column($user->getUserHealthHistory->toArray(), "answer_id");

//            dd($shaAnswerGivenByUserCollection);

            if (in_array($yesAnswerId, $shaAnswerGivenByUserCollection))
                throw new NoRecommendationException();
        }
        return false;
    }

    private function getFormattedTaskResponse(User $user, Collection $tasks)
    {
        $stopToContinueNextWeekTask = false;
        $taskResponse = [];
        $regimenIdsUserDoing = array_column($user->doingTask->toArray(), "regimen_id");
        foreach ($tasks->getIterator() as $regimen_name => $task_group) {
            $tasks = [];
            foreach ($task_group as $key => $task) {
                $userTaskDetails = $user->doingTask()->where('regimen_id', $task->id)
                    ->where('user_id', $user->id)->first();

//                dd($userTaskDetails);

                if ($userTaskDetails == null || $userTaskDetails->start_date == null) {
                    $predicted_week_number = -1;
                    $predicted_today = -1;
                } else {

                    $dateDifference = date_diff($this->helpers->date, new \DateTime($userTaskDetails->new_start_date));
                    $day = ($dateDifference->days % 7) + 1;
                    $predicted_week_number = (int)($dateDifference->days / 7) + 1;
                    $predicted_today = $day;

                    if($predicted_week_number > 1){
                        $regimenObject = $userTaskDetails->regimenInfo->hasWeeklyTasks()->where('week', $predicted_week_number)->first();
                        $taskTracker = $userTaskDetails->taskTracker()->where('week', $predicted_week_number-1)->orderBy('week',"DESC")->first();

                        if(isset($taskTracker) && isset($regimenObject) && $taskTracker->week_percentage < $regimenObject->y){
                            $stopToContinueNextWeekTask = true;
                            $predicted_week_number = $predicted_week_number-1;
                            $predicted_today = 1;
                            $taskTracker->days_status = array_fill(0,6,0);
                            $taskTracker->week_percentage = 0;
                            $taskTracker->save();
                        }
                    }
                }
                $taskDetails = [];
                $weeklyTasks = $task->hasWeeklyTasks;
                foreach ($weeklyTasks as $weeklyTask) {
                    //varies
                    $weekInfo = $userTaskDetails == null ? null : $userTaskDetails->taskTracker()->where('week', $weeklyTask->week)->first();
                    $taskDetails['advise'][] = $weeklyTask->advise;
                    $taskDetails['day_status'][] = $weekInfo !== null ? $weekInfo['days_status'] : array_fill(0, 7, 0);
                    // Fixed
                    $dayTitles = [
                        $weeklyTask->day1_title, $weeklyTask->day2_title, $weeklyTask->day3_title,
                        $weeklyTask->day4_title, $weeklyTask->day5_title, $weeklyTask->day6_title,
                        $weeklyTask->day7_title
                    ];
                    $taskDetails['day_title'][] = $dayTitles;
                    $taskDetails['week_detail'][] = $weeklyTask->week_detail;
                    $taskDetails['badge'][] = $weeklyTask->image;
                    $taskDetails['weekly_task_numbers'][] = count($dayTitles) - count(array_keys($dayTitles, "rest"));
                    // varies
                    $taskDetails['weekly_percentage'][] = $weekInfo !== null ? $weekInfo['week_percentage'] : 0;
                }
                $tasks[] = [
                    "task_id" => $task->id,
                    "task_code" => $task->code,
                    "task_name" => $task->task_name,
                    "title" => $task->title,
                    "is_doing" => in_array($task->id, $regimenIdsUserDoing) ? 1 : -1,
                    // Duplicate keys [But need to maintain previous developer response]
                    'week_number' => $predicted_week_number,
                    'today' => $predicted_today,
                    'predicted_week_number' => $predicted_week_number,
                    'predicted_today' => $predicted_today,
                    "weekly_tasks" => empty($taskDetails) ? (object)[] : $taskDetails,
                ];
            }
            $taskResponse[] = [
                "regimenName" => $regimen_name,
                "tasks" => $tasks
            ];
        }
        return [
            'stopToContinueNextWeekTask'    => $stopToContinueNextWeekTask,
            'taskResponse'  =>  $taskResponse
        ];
    }

    /**
     * @param int $taskId
     * @param int $userId
     * @param bool $isMixedBag
     * @return array
     * @throws TaskAlreadyDoneException
     * @throws UserNotFoundException
     * @throws \Exception
     */
    function dailyTaskDone(int $taskId, int $userId, bool $isMixedBag)
    {
        $user = $this->userRepo->getUser($userId);
        DB::beginTransaction();
        try {
            $extractNextStepInfo = $this->saveUserTaskRegisterInfo($user, $taskId);
            $userDoingTaskObj = $extractNextStepInfo["userDoingTaskObj"];
            $predictedWeek = $extractNextStepInfo["predictedWeek"];
            $day = $extractNextStepInfo["day"];
            $dayMessage = $extractNextStepInfo["dayMessage"];
            $category = $extractNextStepInfo["category"];
            $this->updateAchievementsAndFeeds($user->id, $taskId, $predictedWeek, $day);
            $this->updateUserCategoryModuleScore($user, $userDoingTaskObj->regimenInfo->level, $category, $taskId);
            //Commit Finally all transaction
            DB::commit();
        } catch (TaskAlreadyDoneException $e) {
            throw new TaskAlreadyDoneException();
        } catch (\Exception $e) {
            // Something went wrong, please rollback each transaction
            DB::rollBack();
            Log::error($e->getMessage());
            throw  new \Exception($e);
        }
        $categoryScore = $category === Constants::PHYSICAL ? "physical_score" : strtolower($category) . "_score";
        $weekDayBadgeImage = $this->loadTaskSpecificWeekDayBadge($taskId, $predictedWeek, $day);
        $responseArr = [
            'user_id' => $user->id,
            'regimenBadge' => $extractNextStepInfo["regimenBadge"],
            'regimenWeekBadge' => $extractNextStepInfo["regimenWeekBadge"],
            'regimenDayBadge' => $weekDayBadgeImage,
            'message' => $dayMessage,
            'category_score' => $user->taskInformation->$categoryScore,
            'overall_score' => $user->taskInformation->overall_score
        ];
        return $responseArr;
    }

    // In order to preserve previous developer response format

    /**
     * @param User $user
     * @param int $taskId
     * @return array
     * @throws GlobalException
     * @throws TaskAlreadyDoneException
     * @throws UserNotRegisteredForTaskException
     * @throws \Exception
     */
    private function saveUserTaskRegisterInfo(User $user, int $taskId)
    {
        $isRegimenCompletedByUser = false;
        $weekStatus = false;
        //regimen_id  = code
        if (!in_array($taskId, array_column($user->doingTask->toArray(), "regimen_id"))) {
            throw  new UserNotRegisteredForTaskException();
        }
        $userDoingTaskObj = $user->doingTask()->where('regimen_id', $taskId)->first();
        $weeksToDo = $userDoingTaskObj->regimenInfo->hasWeeklyTasks()->count();
        if (!$this->isUserEligibleToDoTask($userDoingTaskObj, $weeksToDo))
            throw new GlobalException();
        if ($userDoingTaskObj->start_date == null && $userDoingTaskObj->taskTracker()->count() == 0) {
            $predictedWeek = 1;
            $day = 1;
            $regimenObject = $userDoingTaskObj->regimenInfo->hasWeeklyTasks()->where('week', $predictedWeek)->first();
            $dayColumnMessage = "day1_message";
            $dayMessage = $regimenObject->$dayColumnMessage;
            $userDoingTaskObj->user_id = $user->id;
            $userDoingTaskObj->start_date = $this->helpers->date->format("y-m-d");
            $userDoingTaskObj->new_start_date = $this->helpers->date->format("y-m-d");
            $taskTracker = new UserTaskTracker();
            $taskTracker->user_task_id = $userDoingTaskObj->id;
            $taskTracker->week = $predictedWeek;
            $taskTracker->week_percentage = (int)(1 / 7 * 100);
            $days_status = array_fill(0, 7, 0);
            $days_status[0] = $day;
            $taskTracker->days_status = $days_status;
        } else {
            $dateDifference = date_diff(new \DateTime($this->helpers->date), new \DateTime($userDoingTaskObj->start_date));
            $day = $dateDifference->days % 7;
            $predictedWeek = (int)($dateDifference->days / 7) + 1;
            $regimenObject = $userDoingTaskObj->regimenInfo->hasWeeklyTasks()->where('week', $predictedWeek)->first();
            $dayColumnMessage = "day" . ($day + 1) . "_message";
            $dayMessage = $regimenObject->$dayColumnMessage;
            $taskTracker = $userDoingTaskObj->taskTracker()->where('week', $predictedWeek)->first();
            $taskTracker = $taskTracker == null ? new UserTaskTracker() : $taskTracker;
            $days_status = empty($taskTracker->days_status) ? array_fill(0, 7, 0) : $taskTracker->days_status;
            if ($days_status[$day] == 1)
                throw new TaskAlreadyDoneException();
            $days_status[$day] = 1;
            $taskTracker->days_status = $days_status;
            $taskTracker->user_task_id = $userDoingTaskObj->id;
            $taskTracker->week = $predictedWeek;
            $taskTracker->week_percentage = (int)(count(array_keys($days_status, 1)) / 7 * 100);
            if ($taskTracker->week_percentage >= $regimenObject->y && !$taskTracker->week_status) {
                $weekStatus = true;
            }
            // day == 6 is equal to day 7 as start day is counted as 0
            if ($predictedWeek == $weeksToDo && $day == 6) {
                $isRegimenCompletedByUser = true;
            }
        }
        $taskTracker->week_status = $weekStatus;
        $taskTracker->save();
        $userDoingTaskObj->last_done_date = $this->helpers->date->format("y-m-d");
        $userDoingTaskObj->reset_week_counter = 2;
        $category = strtolower($userDoingTaskObj->regimenInfo->getTaskCategory->name);
        $categoryId = $userDoingTaskObj->regimenInfo->getTaskCategory->id;
        $level = $userDoingTaskObj->regimenInfo->level;
        if ($isRegimenCompletedByUser) {
            $userDoingTaskObj->is_regimen_completed = true;
            $this->updatePhysicalLevelTracker($user, $level);
            $this->increaseUserPhysicalTaskLevelIfNeeded($user, $categoryId, $level);
        }
        $userDoingTaskObj->save();
        return [
            'category' => $category,
            'regimenBadge' => $isRegimenCompletedByUser ? $this->getRegimenBadge($taskId) : null,
            'regimenWeekBadge' => $weekStatus ? $this->getRegimenWeekBadge($taskId, $predictedWeek) : null,
            'day' => $day,
            'userDoingTaskObj' => $userDoingTaskObj,
            'predictedWeek' => $predictedWeek,
            'dayMessage' => $dayMessage,
        ];
    }

    /**
     * @param $userDoingTaskObj
     * @param $weekToDo
     * @return bool
     * @throws \Exception
     */
    private function isUserEligibleToDoTask($userDoingTaskObj, $weekToDo)
    {
        if ($userDoingTaskObj->is_regimen_completed)
            return false;
        $dateDifference = date_diff($this->helpers->date, new \DateTime($userDoingTaskObj->start_date));
        $predictedWeek = (int)($dateDifference->days / 7) + 1;
        if ($predictedWeek > $weekToDo)
            return false;
        return true;
    }

    private function updatePhysicalLevelTracker(User $user, int $level)
    {
        $physicalTaskLevelTrackerObj = $user->physicalLevelTaskTracker()->where("user_id", $user->id)
            ->where("level", $level)
            ->first();
        if ($physicalTaskLevelTrackerObj === null) {
            $physicalTaskLevelTrackerObj->task_completed += 1;
        } else {
            $physicalTaskLevelTrackerObj->user_id = $user->id;
            $physicalTaskLevelTrackerObj->level = $level;
            $physicalTaskLevelTrackerObj->task_completed = 1;
        }
        $physicalTaskLevelTrackerObj->save();
        return true;
    }

    private function increaseUserPhysicalTaskLevelIfNeeded(User $user, int $categoryId, int $level)
    {
        $totalTaskOfGivenLevel = $this->taskBankRepo->getTaskCountByLevelAndCategory($categoryId, $level);
        $noOfTaskCompletedByUser = $user->physicalLevelTaskTracker()->where("user_id", $user->id)
            ->where("level", $level)
            ->first()->task_completed;
        if ($noOfTaskCompletedByUser == $totalTaskOfGivenLevel) {
            $user->taskInformation->physical_level += 1;
            $user->taskInformation->save();
        }
        return;
    }

    /**
     * @param int $regimenId
     * @return mixed
     * @throws \App\Exceptions\RegimenNotFoundException
     */
    private function getRegimenBadge(int $regimenId)
    {
        $regimenObject = $this->taskBankRepo->getRegimenById($regimenId);
        return $regimenObject->image;
    }

    /**
     * @param int $regimenId
     * @param int $week
     * @return
     * @throws \App\Exceptions\RegimenNotFoundException
     */
    private function getRegimenWeekBadge(int $regimenId, int $week)
    {
        $regimenObject = $this->taskBankRepo->getRegimenById($regimenId);
        $weekObj = $regimenObject->hasWeeklyTasks()->where('regimen_id', $regimenId)
            ->where('week', $week)
            ->first();
        return $weekObj->image;
    }

    private function updateAchievementsAndFeeds($userId, $taskBankId, $weekNo, $day)
    {
        try {
            $weekTaskObject = $this->weeklyTaskRepo->getWeekTaskObject($taskBankId, $weekNo);
            $dayImageColumn = "day$day" . "_badge";
            Log::info("Badge Image : " . $weekTaskObject->$dayImageColumn . " Image  $dayImageColumn");
            if ($day == 7) {
                $achievementData = [
                    [
                        'user_id' => $userId,
                        'badge_url' => $weekTaskObject->$dayImageColumn
                    ],
                    [
                        'user_id' => $userId,
                        'badge_url' => $weekTaskObject->image
                    ]
                ];
                UserAchievements::insert($achievementData);
            } else {
                UserAchievements::updateOrCreate([
                    'user_id' => $userId,
                    'badge_url' => $weekTaskObject->$dayImageColumn
                ]);
            }
            $this->feedsController->addUsersFeeds($userId, $day, $weekNo, $taskBankId);
        } catch (\Exception $e) {
            Log::warning("Exception Occurred while updating user achievements" . $e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * Update User Specific Category Score and Overall Score
     * @param $userObj
     * @param $taskLevel
     * @param $category
     * @param $taskId
     * @return bool
     */
    private function updateUserCategoryModuleScore($userObj, $taskLevel, $category, $taskId)
    {
        DB::beginTransaction();
        try {
            if (strtolower($category) === Constants::PHYSICAL) {
                $completedTaskColumnToUpdate = "physical_task_completed";
                $scoreColumnToUpdate = "physical_score";
            } else {
                $completedTaskColumnToUpdate = strtolower($category) . "_task_completed";
                $scoreColumnToUpdate = strtolower($category) . "_score";
            }
            $userObj->taskInformation->$completedTaskColumnToUpdate += 1;
            $noOfTaskCompletedByUserInThisModule = $userObj->taskInformation->$completedTaskColumnToUpdate;
            $currentTaskScore = $this->scoreCalculatorService->getUserCurrentModuleScore($taskLevel, $noOfTaskCompletedByUserInThisModule);
            $userObj->taskInformation->$scoreColumnToUpdate = $userObj->taskInformation->$scoreColumnToUpdate + $currentTaskScore;
            $userObj->taskInformation->overall_score = $userObj->taskInformation->overall_score + $currentTaskScore;
            $userObj->taskInformation->save();
            $this->updateUserRegimenScore($userObj, $taskId, $currentTaskScore);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning("Exception Occurred while updating user object", $e->getTrace());
            return false;
        }
        return true;
    }

    private function updateUserRegimenScore($userObj, $taskId, $score)
    {
        $userRegimenScoreObj = $userObj->regimenScore()->where('regimen_id', $taskId)->first();
        if ($userRegimenScoreObj == null)
            $userRegimenScoreObj = new UserRegimenScore();
        $userRegimenScoreObj->regimen_id = $taskId;
        $userRegimenScoreObj->user_id = $userObj->id;
        $userRegimenScoreObj->task_completed += 1;
        $userRegimenScoreObj->secured_score += $score;
        return $userRegimenScoreObj->save();
    }

    // Update user

    /**
     * @param $taskBankId
     * @param $weekNo
     * @param $day
     * @return string
     * @throws \App\Exceptions\RegimenNotFoundException
     */
    public function loadTaskSpecificWeekDayBadge($taskBankId, $weekNo, $day)
    {
        $columnToFetch = "day$day" . "_badge";
        $regimenObject = $this->taskBankRepo->getRegimenById($taskBankId);
        $weekObject = $regimenObject->hasWeeklyTasks->where('taskBank_id', $regimenObject->code)
            ->where('week', $weekNo)
            ->first();
        return $weekObject->$columnToFetch;
    }

    /**
     * @param int $userId
     * @param int $taskId
     * @param bool $isMixedBag
     * @return bool
     * @throws UserNotFoundException
     * @throws \Exception
     */
    function unregisterTask(int $userId, int $taskId, bool $isMixedBag)
    {
        $user = $this->userRepo->getUser($userId);
        if ($isMixedBag) {
            $removalStatus = $this->mixedBagService->unsubscribeUserFromMB($userId, $taskId);
            return $removalStatus;
        }
        $userTasks = $user->doingTask;
        $userDoingRegimenIds = array_column($userTasks->toArray(), 'regimen_id');
        if ($userTasks == null || $userTasks != null && !in_array($taskId, $userDoingRegimenIds))
            return false;
        DB::beginTransaction();
        try {
            DB::transaction(function () use ($taskId, $userId) {
                $taskBankObj = $this->taskBankRepo->getRegimenById($taskId);
                $userTask = UserTask::where('user_id', $userId)
                    ->where('regimen_id', $taskId)
                    ->first();
                if (count($userTask->taskTracker) > 0)
                    $userTask->taskTracker()->delete();
                $userTask->delete();
                $taskBankObj->registered_users -= 1;
                $taskBankObj->save();
            });
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw  $exception;
        }
    }

    /**
     * @param int $userId
     * @param string $category
     * @return array|\Illuminate\Http\JsonResponse
     * @throws AgeRestrictionException
     * @throws CategoryNotFoundException
     * @throws NoRecommendationException
     * @throws RestrictionLevelException
     * @throws UserNotFoundException
     */
    function getPopularTask(int $userId, string $category)
    {

        $user = null;
        $user = $this->userRepo->getUser($userId);
        if ($user->assessmentRecord == null || $user->assessmentRecord->finish_state !== 1)
            return $this->mixedBagService->getCategoryMixedBagTasks($user->id, $category);
        $this->isAgeEligibilityCriteriaPass($user->birthday);
        $this->isMentalLevelInterventionState($category, $user->taskInformation->mental_level);
        $this->isHospitalizationIssuePending($user);
        $categoryId = $this->categoryRepo->getCategoryIdByName($category);
        $categoryName = strtolower($category);
        $category = $category === Constants::PHYSICAL ? "physical" : strtolower($category);
        $categoryLevelColumn = $category . "_level";
        $userCategoryLevel = $user->taskInformation->$categoryLevelColumn;
        if ($categoryName === Constants::PHYSICAL) {
            if ($user->restriction != null && $userCategoryLevel > $user->restriction->restriction_level) {
                throw new RestrictionLevelException();
            }
        }
        $tasks = $this->taskBankRepo->getPopularTaskList($categoryId);
        $tasks = new Collection($tasks);
        $taskResponse = $this->getFormattedTaskResponse($user, $tasks);
        $DoingTaskIds = $user->doingTask()->where('regimen_category', $categoryId)->pluck('regimen_id');
        $responseArr = [
            'isMixedBag' => false,
            "Doing_Tasks" => $DoingTaskIds,
            'task' => $taskResponse['taskResponse'],
            'stopToContinueNextWeekTask' => $taskResponse['stopToContinueNextWeekTask']
        ];
        return $responseArr;
    }

    /**
     * @param int $userId
     * @param int $taskId
     * @param bool $isMixedBag
     * @return \Illuminate\Http\JsonResponse|string
     * @throws UserNotFoundException
     * @throws \App\Exceptions\RegimenNotFoundException
     * @throws \Exception
     */
    function registerTask(int $userId, int $taskId, bool $isMixedBag)
    {
        $user = $this->userRepo->getUser($userId);
        if ($isMixedBag) {
            $registerStatus = $this->mixedBagService->register($userId, $taskId);
            if ($registerStatus)
                return "Congratulations, you are registered for mixed bag regimen.";
            else
                return "User already registered for this regimen";
        }
        if ($user->doingTask != null && in_array($taskId, array_column($user->doingTask->toArray(), 'regimen_id')))
            return Constants::TASK_ALREADY_REGISTER;   // already register for this task
        $taskBankObj = $this->taskBankRepo->getRegimenById($taskId);
        $categoryObject = $taskBankObj->getTaskCategory;
        if ($user->doingTask()->where('regimen_category', $categoryObject->id)->count() >= 2)
            return Constants::taskLimitExceeded($categoryObject->name);
        $userTaskObject = new UserTask();
        $userTaskObject->user_id = $userId;
        $userTaskObject->regimen_id = $taskId;
        $userTaskObject->regimen_category = $categoryObject->id;
        $userTaskObject->register_date = date_format($this->helpers->date, "y-m-d");
        DB::beginTransaction();
        try {
            DB::transaction(function () use ($taskBankObj, $userTaskObject) {
                $userTaskObject->save();
                $taskBankObj->registered_users += 1;
                $taskBankObj->save();
            });
            DB::commit();
            return Constants::TASK_SUCCESS_REGISTER;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("Exception Occurred During Registration Task " . $exception->getMessage());
            throw $exception;
        }
    }

    public function getCategoryRegimens(int $category)
    {
        $regimens = $this->taskBankRepo->getCategoryRegimens($category);
        return $this->regimenDataArr($regimens);
    }

    /**
     * @param object $regimens
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    private function regimenDataArr(object $regimens)
    {
        $result = Array();
        $i = 0;
        foreach ($regimens as $taskBank) {
            $result[$i]['ID'] = $taskBank->id;
            $result[$i]['category'] = $taskBank->category;
            $result[$i]['code'] = $taskBank->code;
            $result[$i]['task_name'] = $taskBank->task_name;
            $result[$i]['level'] = (int)$taskBank->level;
            $result[$i]['step'] = (int)$taskBank->step;
            $result[$i]['image'] = $taskBank->image;
            $result[$i]['detail'] = $taskBank->detail;
            $result[$i]['title'] = $taskBank->title;
            $i++;
        }
        return response($result)->withHeaders([
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * @param int $regimenId
     * @param string $fileType
     * @param string $filePath
     * @return
     * @throws \App\Exceptions\RegimenNotFoundException
     */
    function uploadRegimenBadge(int $regimenId, string $fileType, string $filePath)
    {
        $regimen = $this->taskBankRepo->getRegimenById($regimenId);
        $fileName = strtolower($regimen->code);
        $uploadedUrl = $this->uploadToCloudinaryCDN($fileName, $filePath, [
            "folder" => "regimen",
            "overwrite" => true
        ]);
        $regimen->image = $uploadedUrl;
        $regimen->image_type = $fileType;
        return $regimen->save();
    }

    private function uploadToCloudinaryCDN(string $fileName, string $filePath, array $options = [])
    {
        $options = count($options) > 0 ? $options : null;
        $this->cloudinaryService->upload($filePath, $fileName, $options);
        return $this->cloudinaryService->getUrl();
    }

    public function uploadDailyBadge(string $regimenCode, int $week, int $day, string $filePath)
    {
        $weekObject = $this->weeklyTaskRepo->findWeekTaskByWeekNoAndCode($week, $regimenCode);
        $column = "day" . $day . "_badge";
        $fileName = $regimenCode . "day" . $day . "badge";
        $uploadedUrl = $this->uploadToCloudinaryCDN($fileName, $filePath, [
            "folder" => "dailyBadge",
            "overwrite" => true
        ]);
        $weekObject->$column = $uploadedUrl;
        $weekObject->save();
    }

    /**
     * @param string $regimenCode
     * @param array $options
     * @return mixed
     * @throws \App\Exceptions\RegimenNotFoundException
     */
    public function regimenByCode(string $regimenCode, array $options = [])
    {
        return $this->taskBankRepo->getRegimenByCode($regimenCode, $options);
    }

    function regimenWeekDetails(string $regimenCode)
    {
        return $this->taskBankRepo->getRegimenWeekDetails($regimenCode);
    }

    public function weekTaskObject(string $regimenCode, int $weekNo)
    {
        return $this->taskBankRepo->weekTaskObject($regimenCode, $weekNo);
    }

    public function updateRegimenWeek(string $regimenCode, int $week, array $dataToUpdate)
    {
        return $this->weeklyTaskRepo->updateWeekObject($regimenCode, $week, $dataToUpdate);
    }

    public function createNewRegimen(array $regimenData)
    {
        return $this->taskBankRepo->insertRegimen($regimenData);
    }

    public function addWeekTask(array $weekDetails)
    {
        return $this->weeklyTaskRepo->insertWeekTask($weekDetails);
    }

    public function deleteWeekTask(int $week, string $code)
    {
        return $this->weeklyTaskRepo->deleteWeeklyTask($week, $code);
    }

    public function deleteRegimen(string $regimenCode)
    {
        return $this->taskBankRepo->deleteRegimen($regimenCode);
    }

    /**
     * Map 3 States with Level 1,2,3 and simply return
     *
     * @param $state : Tag State
     * @return int : Level
     * @author Mayank Jariwala
     */
    private function mapStateToLevel($state)
    {
        $state = strtolower($state);
        switch ($state) {
            // Bad == 1 | Good == 2 | Excellent == 3
            case 'bad':
                $level = 1;
                break;
            case 'good':
                $level = 2;
                break;
            case 'excellent':
                $level = 3;
                break;
            default:
                // Bad as default
                $level = 1;
                break;
        }
        return $level;
    }

    public function resetUserTaskTacking($userTaskDetails,$predicted_week_number,$predicted_today){

        $taskTracker = $userTaskDetails->taskTracker()->where('week',"<", $predicted_week_number)->orderBy('week',"DESC")->first();

        if(isset($taskTracker)){
            $days = ((7*$predicted_week_number));
            $date = new \DateTime($userTaskDetails->start_date);
            $date->modify('+'.$days.' day');
            $userTaskDetails->start_date = $date->format('Y-m-d');;

            $dateDifference = date_diff($this->helpers->date, new \DateTime($userTaskDetails->start_date));
            $day = ($dateDifference->days % 7) + 1;
            $predicted_week_number = (int)($dateDifference->days / 7) + 1;
            $predicted_today = $day;

            return [
                'predicted_week_number' =>  $predicted_week_number,
                'predicted_today' =>  $predicted_today,
                'start_date' =>  $userTaskDetails->start_date
            ];
        }else{
            return [];
        }
    }
}