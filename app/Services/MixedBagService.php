<?php
/**
 * Created by PhpStorm.
 * User: MayankJariwala
 * Date: 08-May-19
 * Time: 7:13 PM
 */

namespace App\Services;


use App\Helpers;
use App\Model\Category;
use App\Model\MixedBag;
use App\Model\MixedBagUserHistory;
use Illuminate\Support\Facades\Log;

/**
 * Class MixedBagService : This service class hold an business logic of mixed bags related
 * task
 * @package App\Services
 * @author  Mayank Jariwala
 */
class MixedBagService
{
    private $today, $this_week, $task_day;

    public function __construct()
    {
        try {
            $date = new \DateTime('Asia/Kolkata');
            $this->today = $date->format('d-m-Y');
            $this->this_week = (int)$date->format('W');
            $this->task_day = (int)$date->format('N');
        } catch (\Exception $e) {
            Log::error(" Date Time Exception" . $e->getMessage());
        }
    }

    /**
     * @param $userId
     * @param $taskId
     * @return bool
     */
    public function register($userId, $taskId)
    {
        //TaskId = RegimenId for Mixed Bag
        $regimenId = $taskId;
        if (MixedBagUserHistory::userDoingRegimen($userId, $regimenId))
            return false;
        $mbHistory = new MixedBagUserHistory();
        $mbHistory->user_id = $userId;
        $mbHistory->regimen_id = $regimenId;
        $mbHistory->category = MixedBag::getObject($regimenId)->mapper;
        $mbHistory->user_history = $this->initUserObject($regimenId);
        return $mbHistory->save();
    }

    private function initUserObject($regimenId)
    {
        $historyObj = [
            'id' => $regimenId,
            'isMixedBag' => true,
            'last_date' => null,
            'last_week' => null,
            'start_date' => null,
            'next_week_date' => null,
            'start_week' => $this->this_week,
            'on_going_week' => 1,
            'current_day' => -1,
            'back_down_week' => null,
            'week_to_do' => null,
            'task_week_history' => [
                'week' => 1,
                'day' => array_fill(0, 7, 0),
                'week_done_state' => 0,
                'week_progress_percentage' => 0
            ],
            'task_done_state' => 0,
            'task_complete_state' => 0,
            'total_progress_percentage' => 0
        ];
        return json_encode($historyObj);
    }

    public function updateUserMixedBagRegimenObject($userMbObject)
    {
        $userMbHistory = json_decode($userMbObject->user_history);
        if ($userMbHistory->last_date == null) {
            $expectedDay = 1;
            $userMbHistory->start_date = $this->today;
        } else
            $expectedDay = $this->getExpectedDay($userMbHistory->start_date);
        if ($expectedDay == -1)
            return Helpers::getResponse(500, "Something went wrong");
        if ($expectedDay > 7) {
            return $this->resetUserRegimen($userMbObject);
        }
        $expectedDay = $expectedDay % 7 == 0 ? 7 : $expectedDay;
        $history = $userMbHistory->task_week_history;
        if ($history->day[$expectedDay - 1] != 0)
            return Helpers::getResponse(400, "You already did day $expectedDay task");
        $userMbHistory->last_date = $this->today;
        $userMbHistory->current_day = $expectedDay;
        $history->week = 1;
        $history->day[$expectedDay - 1] = 1;
        $y = (int)(array_count_values($history->day)[1] / 7 * 100);
        if ($expectedDay == 7) {
            $history->week_done_state = 1;
            $userMbObject->isComplete = 1;
        } else
            $history->week_done_state = 0;
        $history->week_progress_percentage = $y;
        $userMbHistory->task_week_history = $history;
        $userMbObject->user_history = json_encode($userMbHistory);
        if ($userMbObject->save())
            return Helpers::getResponse(200, 'Task Done', $userMbHistory);
        else
            return Helpers::getResponse(500, 'Updating failed');
    }

    private function getExpectedDay($lastDoneDate)
    {
        try {
            $lastDoneDate = new \DateTime($lastDoneDate);
            $todayDate = new \DateTime($this->today);
            $days_difference = (int)($todayDate->diff($lastDoneDate)->format("%a")) + 1;
            return $days_difference % 7 == 0 ? 7 : $days_difference % 7;
        } catch (\Exception $e) {
            return -1;
        }
    }

    public function resetUserRegimen($userMbObject)
    {
        $userMbObject->user_history = $this->initUserObject($userMbObject->regimen_id);
        $userMbObject->isComplete = 0;
        if ($userMbObject->save())
            return Helpers::getResponse(200, 'Reset Done', json_decode($userMbObject->user_history));
        else
            return Helpers::getResponse(500, 'Resetting failed');
    }

    public function getCategoryMixedBagTasks($userId, $category)
    {
        $category = Category::getCategoryInfo($category);
        if (!$category)
            return Helpers::getResponse(400, "Category not found");
        $task = MixedBag::where('mapper', '=', $category->id)->first();
        $userTask = MixedBagUserHistory::getUserMbObject($userId, $task->id);
        $weekHistory = null;
        $isUserTaskNotNull = false;
        if (!is_null($userTask)) {
            $isUserTaskNotNull = true;
            $weekHistory = json_decode($userTask->user_history);
        }
        $doingTasksArr = MixedBagUserHistory::getUserDoingTasks($userId, $category->id);
        $responseArr = [
            'today' => $this->task_day,
            'Doing_Tasks' => $doingTasksArr,
            // There is no need to structure is such way but as per response set by previous
            // developer in other task response, I have to in order to make it easier for iOS
            // developer to parse the response
            'task' => [
                [
                    'regimenName' => $task->regimen_name,
                    'tasks' => [
                        [
                            'task_id' => $task->id,
                            'is_doing' => in_array($task->id, $doingTasksArr) ? 1 : -1,
                            'task_name' => $task->regimen_name,
                            'title' => $task->regimen_name,
                            'week_number' => 1,
                            'today' => $this->task_day,
                            'predicted_today' => $isUserTaskNotNull ? $this->getExpectedDay($weekHistory->start_date) : -1,
                            'predicted_week_number' => $isUserTaskNotNull ? 1 : -1,
                            'weekly_tasks' => [
                                'day_status' => [
                                    $isUserTaskNotNull ? $weekHistory->task_week_history->day : array_fill(0, 7, 0)
                                ],
                                'day_title' => [
                                    [
                                        $task->day_1,
                                        $task->day_2,
                                        $task->day_3,
                                        $task->day_4,
                                        $task->day_5,
                                        $task->day_6,
                                        $task->day_7,
                                    ],
                                ],
                                'weekly_percentage' => [
                                    $isUserTaskNotNull ? $weekHistory->task_week_history->week_progress_percentage : 0
                                ],
                                'weekly_task_numbers' => [
                                    1
                                ]
                            ],
                        ],
                    ],
                ]
            ],
            'isMixedBag' => true
        ];
        return $responseArr;
    }
}