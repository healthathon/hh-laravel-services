<?php

namespace App\Http\Controllers\Api;

use App\Constants;
use App\Exceptions\UserNotFoundException;
use App\Helpers;
use App\Http\Controllers\Admin\TaskControllerV2;
use App\Http\Controllers\Controller;
use App\Model\Assess\assesHistory;
use App\Model\Category;
use App\Model\Tasks\taskBank;
use App\Model\Tasks\weeklyTask;
use App\Model\User;
use App\Model\UserAchievements;
use App\Model\UserTask;
use App\Services\MixedBagService;
use App\Services\ModuleScoreServices;
use DateTimeZone;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{

    public $user_task, $user, $user_id, $this_week, $task_day, $today, $days_difference, $date;
    public $physical_used_tasks = Array(), $mental_used_tasks = Array(), $nutrition_mental_tasks = Array(), $lifestyle_mental_tasks = Array();
    public $scoreCalculatorService;
    public $feedsController;
    //Mayank Jariwala
    private $mixedBagService, $mixedBagController;

    public function __construct()
    {
        $this->user = new User();
        $this->scoreCalculatorService = new ModuleScoreServices();
        $this->mixedBagService = new MixedBagService();
        $this->mixedBagController = new MixedBagController();
        try {
            $this->date = new \DateTime('', new DateTimeZone(env('DATETIME_ZONE')));
            $this->today = $this->date->format('Y-m-d');
            $this->this_week = (int)$this->date->format('W');
            $this->task_day = (int)$this->date->format('N');
            $this->feedsController = new FeedsController();
        } catch (\Exception $e) {
            Log::error(" DateTime Exception in TaskController Constructor");
        }
        // Commented by Mayank Jariwala : To keep Monday-Sunday as 1 week
//        if ($this->task_day == 7)
//            $this->this_week = $this->this_week - 1;  // Will think this week sunday as last week sunday.
    }


    public function getWeeAndDay($temp)
    {
        $date = new \DateTime($temp);
        $day = $date->format('N');  // day of the week represented as number
        $week = $date->format('W');

        if ($day == 7) {  // Means if $date is sunday
            $week--;
        }

        $result['day'] = $day;
        $result['week'] = $week;
    }

    /**
     *  Modification of function : Added few checks like user cannot register
     * more than 2 task in same assess module and cannot register same
     * task again an again
     *
     * @author  <a href="menickwa@gmail.com">Mayank Jariwala</a>
     * @param Request $request
     * @return UserTask|\Illuminate\Http\JsonResponse|null
     */
    public function taskRegisterRequest(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'userId' => 'required',
            'taskId' => 'required',
            'isMixedBag' => 'required'
        ]);
        if ($validate->fails())
            return Helpers::getResponse(400, "Validation Error", $validate->getMessageBag());
        $task_id = $request->get('taskId');
        $user_id = $request->get('userId');
        $isMixedBag = $request->get('isMixedBag');
        if ($isMixedBag) {
            return $this->mixedBagController->register($request);
        }
        $taskBank = taskBank::find($task_id);
        if ($taskBank) {
            //Get Task Category
            $category = $taskBank->getTaskCategory->name;
            $userTaskObject = UserTask::getUserTask($user_id);
            if (!empty((array)$userTaskObject)) {
                // Fetch Category Name
                $categoryFieldName = $category . '_DoingTasks';
                $onGoingAssessModuleTasks = $userTaskObject->$categoryFieldName;
                if (!empty($onGoingAssessModuleTasks)) {
                    // User cannot register more than 2 task in same assess
                    // Check Already Current Task Registered By User
                    if (count($onGoingAssessModuleTasks) < 2) {
                        if (in_array($task_id, $onGoingAssessModuleTasks)) {
                            return Helpers::getResponse(400, 'You have already registered for this task');
                        }
                    } else {
                        return Helpers::getResponse(400, 'You cannot registered for more than 2 task');
                    }
                }
            }
            return $this->saveRegisterTaskRequest($user_id, $taskBank, $category, $userTaskObject);
        } else {
            return Helpers::getResponse(404, 'No such task found');
        }
    }

    /**
     *  Logic from Function name  "taskRegisterRequest"  is shifted to newly created function
     *
     * @author  <a href="menickwa@gmail.com">Mayank Jariwala</a> : Just Created New Function
     * @param $user_id
     * @param $taskBank
     * @param $category
     * @param null $userTaskObject
     * @return \Illuminate\Http\JsonResponse
     */
    private function saveRegisterTaskRequest($user_id, $taskBank, $category, $userTaskObject = null)
    {
        $task_id = $taskBank->id;
        $today = $this->today;
        // how many weeks need to complete this regimen
        $week_to_do = count($taskBank->hasWeeklyTasks);
        $temp = [
            'id' => $task_id,
            'isMixedBag' => false,
            'start_week' => $this->this_week,
            'start_date' => $this->today,
            'next_week_date' => date('Y-m-d', strtotime($today . ' + 7 days')),
            // how many weeks user has completed by doing this task
            'on_going_week' => 1,
            // day when he started : initially 0th day
            'current_day' => 0,
            // how many weeks users has to do to complete this regimen
            'week_to_do' => $week_to_do,
            'last_date' => null,
            'last_week' => $this->this_week,
            'back_down_week' => $this->this_week,
            'task_week_history' => [],
            'task_done_state' => 0,
            'task_complete_state' => 0,
            'total_progress_percentage' => 0,
        ];
        if (!empty((array)$userTaskObject)) {
            $userTask = $userTaskObject->task;
        } else {
            $userTaskObject = new UserTask();
            $userTaskObject->user_id = $user_id;
            $userTask = [];
        }
        array_push($userTask, $temp);
        $userTaskObject->task = $userTask;
        $columnName = $category . "_DoingTasks";
        $DoingTasks = $userTaskObject->$columnName;
        $DoingTasks[] = $task_id;
        $userTaskObject->$columnName = $DoingTasks;
        if ($userTaskObject->save()) {
            $taskBank->registered_users = $taskBank->registered_users + 1;
            if ($taskBank->save())
                return Helpers::getResponse(200, "Registered for requested regimen", $userTaskObject);
            else
                return Helpers::getResponse(500, "Incrementing registered user failed");
        } else {
            return Helpers::getResponse(500, "Registering for requested regimen failed");
        }
    }

    /** Temporary fixing solutions applied , wherever fixing is done comments is added
     * // Mayank Jariwala (Actual Code Developed by Other Developer)
     * @Deprecated Function
     * @param $userId
     * @return array
     * @throws \Exception
     */
    public function getTask($userId)
    {
        $categories = ["Physics", 'Mental', 'Lifestyle', 'Nutrition'];
        $result = array();
        $result['today'] = $this->task_day;
        for ($i = 0; $i < 4; $i++) {
            $category = $categories[$i];
            $result['Tasks'][$i]['category'] = $category;

            // User tasks api
            $temp = $this->getAllTasks($category, $userId);
            $result['Tasks'][$i]['Doing_Tasks'] = $temp['Doing_Tasks'];
            $result['Tasks'][$i]['all_tasks'] = $temp['task'];

            // Recommendation Api
            $temp = $this->getRecommendedTask($category, $userId);
            $result['Tasks'][$i]['recommended_tasks'] = $temp['task'];

            // Popular Api
            $temp = $this->getPopularTasks($category, $userId);
            $result['Tasks'][$i]['popular_tasks'] = $temp['task'];
        }
        return $result;
    }

    /**
     *  This function is Deprecated
     * @param $userId
     * @param $category
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    // Deprecated
    public function getAllTasks($userId, $category)
    {
        try {
            $user = User::where('id', $userId)->first();
            if (!$user) {
                return response()->json([
                    'statusCode' => 200,
                    'statusMessage' => 'No user found with given id',
                    'response' => null
                ]);
            }
            $user_tasks = null;
            $all_tasks = Array();
            $result = Array();

            $temps = taskBank::where('category', ucfirst($category))->get()->groupBy('task_name');
            $i = 0;
            foreach ($temps as $task_bank_all_steps) {
                foreach ($task_bank_all_steps as $task) {
                    $task_id = $task->id;
                    $check = $this->checkIfDoingTask($userId, $task_id);
                    $is_doing_task = $check['is_doing_task'];
                    $result[$i]['task_id'] = $task_id;
                    $result[$i]['task_name'] = $task->task_name;
                    $result[$i]['title'] = $task->title;
                    $task_index = $check['task_index'];
                    if ($is_doing_task == 1) // If user is doing task
                    {
                        $result[$i]['is_doing'] = 1;
                        if (strtolower($category) == 'physics') {
                            $this->backDownWeek($userId, $task_index);
                        }
                    } elseif ($is_doing_task == -1) {  //This means user did not start task
                        $result[$i]['is_doing'] = -1;
                    } else // This means user finish that task
                        $result[$i]['is_doing'] = 0;
                    $week_state = $this->getTaskWeek($task_id, $task_index, null);
                    $result[$i]['week_number'] = $week_state['week_number'];
                    $result[$i]['today'] = $week_state['today'];
                    $result[$i]['predicted_week_number'] = $week_state['predicted_week_number'];
                    $result[$i]['predicted_today'] = $week_state['predicted_today'];
//                    $result[$i]['day_status']=$week_state['day_status'];
                    $result[$i]['weekly_percentage'] = $week_state['weekly_percentage'];
                    $result[$i]['weekly_task_numbers'] = $week_state['weekly_task_numbers'];
                    $result[$i]['weekly_tasks'] = $week_state['weekly_tasks'];
                    $i++;
                }
            }
            $task_day = $this->task_day;
            $user_task = $this->user_task;
            $Doing_Tasks = $user_task[ucfirst($category) . '_DoingTasks'];
            return response()->json([
                'statusCode' => 200,
                'statusMessage' => 'Tasks',
                'response' => [
                    'today' => $task_day,
                    'Doing_Tasks' => $Doing_Tasks,
                    'task' => $result
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => 500,
                'statusMessage' => 'Server Error',
                'response' => $e->getMessage()
            ]);
        }
    }

    private function checkIfDoingTask($userObj, $taskBankObj, $user_task = null)
    {
        //-1 means that user did not start this task, 0 means user finished task, 1 means user is doing task
        if ($user_task == null)
            return null;
        $is_doing_task = -1;
        //The variable for saving task_id position in user_task
        $task_index = -1;
        $user_id = $userObj->id;
        $task_id = $taskBankObj->id;
        $category = $taskBankObj->getTaskCategory->name;
        $taskLevel = $taskBankObj->level;
        $result = Array();
        $Doing_Tasks = $user_task[$category . '_DoingTasks'];
        if (!is_null($Doing_Tasks) && in_array($task_id, $Doing_Tasks) == false) {
            if (\request()->method() == "POST") {
                // Return this response if user tries to call task done request
                $responseArr['statusCode'] = 404;
                $responseArr['statusMessage'] = "You have not registered for this task";
                return $responseArr;
            } else {
                // Normal get call for Getting all Task
                $result['is_doing_task'] = $is_doing_task;
                $result['task_index'] = $task_index;
                $result['category'] = $category;
                return $result;
            }
        }
        $task1 = is_null($user_task) ? [] : $user_task->task;
        for ($i = 0; $i < count($task1); $i++) {
            if ($task1[$i]['id'] == $task_id) {
                $last_date = $task1[$i]['last_date'];
                $is_doing_task = 1;
                $task_index = $i;
                $total_weeks = weeklyTask::getTaskTotalWeeks($task_id);
                $task_week_history = $task1[$i]['task_week_history'];
                $task_done_state = $task1[$i]['task_done_state'];
                if ($task_done_state == 1) {
                    // This means task is already marked as done
                    $is_doing_task = 0;
                } else {
                    $doing_weeks = count($task_week_history);
                    // -1 Since starting week is counted so I don't have to count next week
                    // Expected week that user should complete this task
                    $expectedWeekNoAsPerISO = $task1[$i]['start_week'] + $total_weeks - 1;
                    if ($total_weeks == $doing_weeks) {
                        // that is the number of done task equals the number of total weeks
//                            $prev_week = $this->getPrevWeek($last_date);
                        $this_week = $this->this_week;
                        $finished_task_index = $this->getFinishedTaskIndex($category, $user_id, $task_id);
                        if ($this_week == $expectedWeekNoAsPerISO && $this->task_day == 7) {
                            Log::info(" Task Id : " . $task_id . " is Completed for user " . $user_id);
                            if ($category == ucfirst(Constants::PHYSICAL)) {
                                if ($task1[$i]['task_completed_state'] == 1) {  // This means task is completed
                                    $task1[$i]['task_done_state'] = 1;  // Will display task as completed
                                    if ($finished_task_index != -1) {
                                        array_slice($Doing_Tasks, $finished_task_index, 1);  //This will remove this task id from doing task array of user task
                                    }
                                    $is_doing_task = 0;
                                }
                            } else {  // For all other categories, task will be completed.
                                $task1[$i]['task_done_state'] = 1;
                                if ($finished_task_index != -1) {
//                                        array_splice($Doing_Tasks, $finished_task_index, 1);  //This will remove this task id from doing task array of user task
                                    unset($Doing_Tasks[$finished_task_index]); // Added by Mayank Jariwala
                                    $userObj->save();
                                }
                                $is_doing_task = 0;
                            }
                        }
                    }
                }
                break;
            }
        }
        $doingTaskColumn = $category . '_DoingTasks';
        $user_task->task = $task1;
        $user_task->$doingTaskColumn = $Doing_Tasks;
        $user_task->save();
        $this->user_task = $user_task;
        $result['is_doing_task'] = $is_doing_task;
        $result['task_index'] = $task_index;
        $result['category'] = $category;
        $result['user_task'] = $user_task;
        return $result;
    }

    /**
     * Goal is to return key of found element
     *
     * @author  Mayank Jariwala
     * @param $category
     * @param $user_id
     * @param $task_id
     * @return false|int|string
     */
    public function getFinishedTaskIndex($category, $user_id, $task_id)
    {  // This function will return the index of finished task
        $user_task = UserTask::where('user_id', $user_id)->get()->first();
        $DoingTasks = $user_task[$category . '_DoingTasks'];

        // Commented By Mayank Jariwala - Code of Other Developer
//        for ($i = 0; $i < count($DoingTasks); $i++) {
//            if ($task_id == $DoingTasks[$i]) {
//
//                $finished_task_index = $DoingTasks[$i];
//                break;
//            }
//        }
//        return $finished_task_index;

        //Added by Mayank Jariwala
        $finished_task_index = array_search($task_id, $DoingTasks);
        if ($finished_task_index === false) {
            return -1;
        }
        return $finished_task_index;
    }

    public function backDownWeek($user_id, $task_index)
    {  //Back Down Weeks, will call this function only when category=Physics

        $user_task = $this->getUserTask($user_id);
        $today = $this->today;
        $this_week = $this->this_week;

        $task1 = $user_task->task;
        $task = $task1[$task_index];
        $task_id = $task['id'];

        $task_week_history = $task['task_week_history'];
        $last_date = $task['last_date'];
        $back_down_week = $task['back_down_week'];
        $prev_week = $this->getPrevWeek($last_date);

        if ($task['task_done_state'] != 1) {  // This means if task is not finished{

            if (($this_week - $prev_week) == 1) {

                if ($this_week != $back_down_week) {  //This means back down not finished in this week
                    if (!empty($task_week_history)) {
                        if ($task_week_history[count($task_week_history) - 1]['week_done_state'] == 0) {
                            array_splice($task_week_history, count($task_week_history) - 1, 1);
                            $task['back_down_week'] = $this_week;
                        }
                    }
                }
            }

            if ($this_week - $prev_week == 2) {
                $diff = $back_down_week - $prev_week;
                if ($diff == 0) {  //0 means back down did not occur, 1 means back down did before 1 week, 2 means back down did not occur at all
                    if (!empty($task_week_history)) {
                        array_splice($task_week_history, count($task_week_history) - 1, 1);
                        $task['back_down_week'] = $this_week;
                    }
                }
            }
            if ($this_week - $prev_week >= 3) {
                $diff = $back_down_week - $prev_week;
                $n = 0;
                if ($diff >= 2)  // This means backdown progrecced after 2 or 1 weeks later since last task week
                    $n = 2;  // 2 week backdown
                if ($diff == 0)  // This means backdown not progreceed at all since last task week
                    $n = 3;  // 3 week back down

                for ($i = 0; $i < $n; $i++) {
                    if (!empty($task_week_history))
                        array_splice($task_week_history, count($task_week_history) - 1, 1);
                }
                $task['back_down_week'] = $this_week;
            }
        }

        $task['task_week_history'] = $task_week_history;

        $task1[$task_index] = $task;

        $user_task->task = $task1;
        $user_task->save();
        $this->user_task = $user_task;

    }

    function getUserTask($user_id)
    {
        $user_task = null;
        $temps = UserTask::where('user_id', $user_id)->first();
        if (!is_null($temps))
            return $temps;
        return $user_task;
    }

    public function getPrevWeek($last_date)
    {
        $date = new \DateTime($last_date);
        $prev_week = $date->format('W');
        $day = $date->format('N');

        // Commented by Mayank Jariwala : To keep Monday-Sunday as 1 week
//        $day = $date->format('N');
//        if ($day == 7) //Means if sunday
//            $prev_week = $prev_week - 1;
        return $prev_week;
    }

    /**
     * @param $task_id
     * @param $task_index
     * @param $user_task
     * @return array
     * @throws \Exception
     */
    public function getTaskWeek($task_id, $task_index, $user_task)
    {  //This will return current task week.
        try {
            $current_week = 0;
            $this_week = $this->this_week;
            $task_day = $this->task_day;
            $week_number = 0;
            $day_status = array(0, 0, 0, 0, 0, 0, 0);
            $result = array();
            $weekly_percentage = Array();
            $weekly_tasks = weeklyTask::where('taskBank_id', $task_id)->orderBy('week')->get();
            for ($i = 0; $i < count($weekly_tasks); $i++) {
                $weekly_percentage[$i] = 0;
                for ($j = 0; $j < 7; $j++) {
                    $result['weekly_tasks']['day_status'][$i][$j] = 0;
                }
            }
            if ($task_index != -1) {
                $task1 = $user_task->task;
                $task = $task1[$task_index];
                if (array_key_exists('start_date', $task)) {
                    $start_date = new \DateTime($task['start_date'], new DateTimeZone(env('DATETIME_ZONE')));
                    $today = new \DateTime($this->today, new DateTimeZone(env('DATETIME_ZONE')));
                    $expected_week = floor($start_date->diff($today)->days / 7) + 1;
                    $this->days_difference = (int)($today->diff($start_date)->format("%a")) + 1;
                    $expected_today = $this->days_difference % 7 == 0 ? 7 : $this->days_difference % 7;
                    $result['today'] = $task['current_day'];
                    $result['week_number'] = $task['on_going_week'];
                    $result['predicted_week_number'] = $expected_week;
                    $result['predicted_today'] = $expected_today;
                }
                $task_week_history = $task['task_week_history'];
                if (!empty($task_week_history)) {
                    $current_week_number = max(array_keys($task_week_history));
//                    $this->getDoingWeekNumbers($task_week_history);
                    $start_date = new \DateTime($task['start_date'], new DateTimeZone(env('DATETIME_ZONE')));
                    $todayDate = new \DateTime($this->today, new DateTimeZone(env('DATETIME_ZONE')));
                    $this->days_difference = (int)($todayDate->diff($start_date)->format("%a")) + 1;
                    $current_week = round($this->days_difference / 7);
                    $current_week_task = $task_week_history[$current_week_number];
                    $last_date = $task['last_date'];
                    $prev_week = $this->getPrevWeek($last_date);
                    if ($this_week == $prev_week) {  // That is, current week equals to last task done week
                        $week_number = $current_week_number == 0 ? 1 : $current_week_number;
                        $week_task_days = $current_week_task['day'];
                        for ($i = 0; $i < count($week_task_days); $i++) {
                            $index = $week_task_days[$i];
                            $day_status[$index - 1] = 1;
                        }
                    } else {
                        $week_number = $current_week_number + 1;  // Increase week number
                    }
                    for ($i = 0; $i < $week_number; $i++) {
                        //Get the weekly percentage
                        // Since During Task save index is from 1 [ From 1 is done for some reason]
                        if (array_key_exists($i, $task_week_history)) {
                            $current_week_task = $task_week_history[$i];
                            $week_task_days = $current_week_task['day'];
//                            print_r($week_task_days);
                            for ($j = 0; $j < count($week_task_days); $j++) {
                                $index = $week_task_days[$j];
                                $result['weekly_tasks']['day_status'][$i][$index - 1] = 1;
                            }
                            // Since During Task save index is from 1 [ From 1 is done for some reason]
                            $weekly_percentage[$i] = $task_week_history[$i]['week_progress_percentage'];
                        }
                    }
                }
            } else {
                // Added by Mayank Jariwala
                $result['today'] = -1;
                $result['week_number'] = -1;
                $result['predicted_week_number'] = -1;
                $result['predicted_today'] = -1;
            }

//        $result['day_status']=$day_status;
            $result['weekly_percentage'] = $weekly_percentage;
            $i = 0;
            foreach ($weekly_tasks as $weekly_task) {
                $temp = $this->getTotalTaskDay($task_id, $i + 1);
                $result['weekly_task_numbers'][$i] = (int)$temp['total_day'];
                for ($j = 1; $j <= 7; $j++) {
                    $result['weekly_tasks']['day_title'][$i][$j - 1] = $weekly_task['day' . $j . '_title'];
                }
                $result['weekly_tasks']['week_detail'][$i] = $weekly_task['week_detail'];
                if (!is_null($weekly_task['badge']))
                    $result['weekly_tasks']['badge'][$i] = url('public/badges/' . $weekly_task['badge']);
                else
                    $result['weekly_tasks']['badge'][$i] = url('public/badges/default.png');
                $i++;
            }
            return $result;
        } catch (\Exception $e) {
            throw  new \Exception($e);
        }
    }

    public function getTotalTaskDay($task_id, $week)
    {  // Will get the whole task day except for Rest days
        $temp = weeklyTask::where([['taskBank_id', $task_id], ['week', $week]])->first();
        $taskBank = taskBank::getTaskBankObject($task_id);
        $category = $taskBank->getTaskCategory->name;
        $total_day = 0;
        $y = 0;
        $result = Array();
        if ($temp == null) {
            return $result;
        }
        if ($category == 'Physics') {
            for ($i = 1; $i <= 7; $i++) {
                $aa = strripos($temp['day' . $i . '_title'], 'rest');
                if ($aa === false) {
                    $total_day++;
                }
            }
            $y = 75;
        } else {
            $total_day = $temp->x;
            $y = $temp->y;
        }
        $result['total_day'] = $total_day;
        $result['y'] = $y;
        return $result;
    }

    /**
     * @param $userId
     * @param $category
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getRecommendedTask($userId, $category)
    {
        $user = null;
        try {
            $user = User::getUser($userId);
            if ($user->assessmentRecord == null || $user->assessmentRecord->finish_state !== 1)
                return $this->mixedBagService->getCategoryMixedBagTasks($userId, $category);
            $task_day = $this->task_day;
            $category = strtolower($category);
            $categoryId = Category::where('name', ucfirst($category))->first()->id;
            $level = $this->mapStateToLevel(assesHistory::getUserTagState($category, $user));
            if ($category !== Constants::PHYSICAL) {
                $categoryRegimensId = taskBank::where('category', $categoryId)->pluck('id')->toArray();
                $recommendedTaskId = array_column($user->recommendedTask->toArray(), 'regimen_id');
                $recommendedTaskId = array_intersect($categoryRegimensId, $recommendedTaskId);
                $temps = taskBank::whereIn('id', $recommendedTaskId)
                    ->orderBy('step')
                    ->get()
                    ->groupBy('task_name');
            } else {
                $temps = taskBank::where('category', $categoryId)
                    ->where('level', $level)
                    ->orderBy('step')
                    ->get()
                    ->groupBy('task_name');
            }
            $userTask = $user->doingTask;
            $Doing_Tasks = null;
            $returnObjectArr = $this->getRecommendationOrPopularTaskObject($temps, $category, $user, $userTask);
            $objectArr = [
                'today' => $task_day,
                'isMixedBag' => false,
                'Doing_Tasks' => $returnObjectArr['doing_task'],
                'task' => $returnObjectArr['result']
            ];
            return Helpers::getResponse(200, 'Recommended', $objectArr);
        } catch (UserNotFoundException $e) {
            $e->report($userId);
            $e->setMessage("User not found");
            return Helpers::getResponse(404, $e->getMessage());
        } catch (\Exception $e) {
            Log::error("Exception" . $e->getTraceAsString());
            return Helpers::getResponse(500, "Server Error", $e->getLine() . " " . $e->getMessage());
        }
    }

    /**
     * Map 3 States with Level 1,2,3 and simply return
     *
     * @author Mayank Jariwala
     * @param $state : Tag State
     * @return int : Level
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

    private function getRecommendationOrPopularTaskObject($temps, $category, $user, $userTask)
    {
        $userId = $user->id;
        $result = [];
        $i = 0;
        $Doing_Tasks = [];
        foreach ($temps as $key => $task_bank_all_steps) {
            $result[$i]["regimenName"] = $key;
            foreach ($task_bank_all_steps as $task) {
                $task_id = $task->id;
                $taskBankObj = taskBank::getTaskBankObject($task_id);
                $weeklyTaskResult = $this->getWeeklyTaskForRegimen($task_id);
                $week_state = [
                    'week_number' => -1,
                    'today' => $this->task_day,
                    'predicted_week_number' => -1,
                    'predicted_today' => -1,
                    'weekly_tasks' => [
                        'day_status' => $weeklyTaskResult['day_status'],
                        'day_title' => $weeklyTaskResult['day_title'],
                        'week_detail' => $weeklyTaskResult['week_detail'],
                        'badge' => $weeklyTaskResult['badge']
                    ],
                    'weekly_percentage' => $this->getWeeklyTaskInitPercentages($task_id),
                    'weekly_task_numbers' => $this->getWeeklyTaskInitNumbers($task_id)
                ];
                $is_doing_task = -1;
                if (!is_null($userTask)) {
                    $Doing_Tasks = $userTask[ucfirst($category) . '_DoingTasks'];
                    $check = $this->checkIfDoingTask($user, $taskBankObj, $userTask);
                    $is_doing_task = $check['is_doing_task'];
                    if ($is_doing_task == 1 || $is_doing_task == -1) {  // This means user is doing task or not started
                        $task_index = $check['task_index'];
                        if ($is_doing_task == 1) // If user is doing task
                        {
                            if ($category == 'physics')
                                $this->backDownWeek($userId, $task_index);
                        }
                        try {
                            $week_state = $this->getTaskWeek($task_id, $task_index, $check['user_task']);
                        } catch (\Exception $e) {
                            Log::error(" Exception Occurred " . $e->getMessage());
                        }
                    }
                }
                $result[$i]["tasks"][] = [
                    'task_id' => $task_id,
                    'task_name' => $task->task_name,
                    'title' => $task->title,
                    'is_doing' => $is_doing_task,
                    'week_number' => $week_state['week_number'],
                    'today' => $week_state['today'],
                    'predicted_week_number' => $week_state['predicted_week_number'],
                    'predicted_today' => $week_state['predicted_today'],
                    'weekly_tasks' => [
                        'day_status' => $week_state["weekly_tasks"]['day_status'],
                        'day_title' => $week_state["weekly_tasks"]['day_title'],
                        'week_detail' => $week_state["weekly_tasks"]['week_detail'],
                        'badge' => $week_state["weekly_tasks"]['badge'],
                        'weekly_percentage' => $week_state['weekly_percentage'],
                        'weekly_task_numbers' => $week_state['weekly_task_numbers'],
                    ],
                ];
            }
            $i++;
        }
        return [
            'doing_task' => $Doing_Tasks,
            'result' => $result
        ];
    }

    private function getWeeklyTaskForRegimen($regimenId)
    {
        $result = [];
        $weekly_tasks = weeklyTask::where('taskBank_id', $regimenId)->orderBy('week')->get();
        for ($i = 0; $i < count($weekly_tasks); $i++) {
            for ($j = 0; $j < 7; $j++) {
                $result['day_status'][$i][$j] = 0;
            }
        }
        $i = 0;
        foreach ($weekly_tasks as $weekly_task) {
            $temp = $this->getTotalTaskDay($regimenId, $i + 1);
            for ($j = 1; $j <= 7; $j++) {
                $result['day_title'][$i][$j - 1] = $weekly_task['day' . $j . '_title'];
            }
            $result['week_detail'][$i] = is_null($weekly_task['week_detail']) ? null : $weekly_task['week_detail'];
            if (!is_null($weekly_task['badge']))
                $result['badge'][$i] = url('public/badges/' . $weekly_task['badge']);
            else
                $result['badge'][$i] = url('public/badges/default.png');
            $i++;
        }
        return $result;
    }

    private function getWeeklyTaskInitPercentages($regimenId)
    {
        $count = weeklyTask::getTaskTotalWeeks($regimenId);
        return array_fill(0, $count, 0);
    }

    private function getWeeklyTaskInitNumbers($regimenId)
    {
        $result = [];
        $count = $this->getTotalWeeks($regimenId);
        for ($i = 0; $i < $count; $i++) {
            $temp = $this->getTotalTaskDay($regimenId, $i + 1);
            $result[$i] = (int)$temp['total_day'];
        }
        return $result;
    }

    public function getTotalWeeks($task_id)
    {  // This will get the total number of week of this task
        return weeklyTask::where('taskBank_id', $task_id)->count();
    }

    public function getPopularTasks($userId, $category)
    {
        try {
            $user = User::getUser($userId);
            if ($user->assessmentRecord == null || $user->assessmentRecord->finish_state !== 1)
                return $this->mixedBagService->getCategoryMixedBagTasks($userId, $category);
            $categoryId = Category::getCategoryInfo(ucfirst($category))->id;
            $temps = taskBank::where('category', '=', $categoryId)
                ->where('registered_users', '>=', 10)
                ->orderBy('registered_users', 'desc')
                ->get()
                ->groupBy('task_name');
            $task_day = $this->task_day;
            $category = strtolower($category);
            $userTask = UserTask::getUserTask($userId);
            $returnObjectArr = $this->getRecommendationOrPopularTaskObject($temps, $category, $user, $userTask);
            $objectArr = [
                'today' => $task_day,
                'isMixedBag' => false,
                'Doing_Tasks' => $returnObjectArr['doing_task'],
                'task' => $returnObjectArr['result']
            ];
            return Helpers::getResponse(200, "Popular Tasks", $objectArr);
        } catch (UserNotFoundException $e) {
            $e->report($userId);
            $e->setMessage("User not found");
            return Helpers::getResponse(404, $e->getMessage());
        } catch (\Exception $e) {
            return Helpers::getResponse(500, "Server Error", $e->getMessage());
        }
    }


    /**
     *  Mark One day as Completed for user daily task
     *  V2 of SaveTaskDone (@Deprecated)
     * @param Request $request
     * @author Mayank Jariwala
     * @return \Illuminate\Http\JsonResponse
     */
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
        if ($isMixedBag)
            return $this->mixedBagController->taskComplete($request);
        try {
            $userObj = User::getUser($userId);
            $user_task = $userObj->doingTask;
            if (is_null($user_task))
                return Helpers::getResponse(404, "User not registered for any regimen");
            $taskBank = taskBank::getTaskBankObject($taskId);
            $category = $taskBank->getTaskCategory->name;
            $columnName = $category . "_DoingTasks";
            // Check Whether Doing the task
            if (in_array($taskId, $user_task->$columnName)) {
                //Means the number of weeks of current task
                $total_weeks = weeklyTask::getTaskTotalWeeks($taskId);
                $tasks = $user_task->task;
                $current_task = null;
                $foundIndex = -1;
                foreach ($tasks as $task) {
                    ++$foundIndex;
                    if ($task["id"] == $taskId) {
                        $current_task = $task;
                        break;
                    }
                }
                //the last day of last done task
                $pre_last_date = new \DateTime($current_task['last_date'], new DateTimeZone(env('DATETIME_ZONE')));
                //Last date task submission is today then task is already been submitted -
                // Today's Task Done, then response is sent from here
                if ($current_task['last_date'] !== null && $pre_last_date->format("d-m-Y") === $this->date->format("d-m-Y")) {
                    return Helpers::getResponse(200, "Already Completed Task");
                }
                $task_week_history = $current_task['task_week_history'];
                $taskLevel = $taskBank->level;
                $current_task = $this->updateTaskWeekHistory($current_task);
                // $diff : Day from 1 to 7 and (multiple of 7) % 7 == 0 , so its 7th day of user
//                $current_index = count($current_task['task_week_history']);
                // On going week : 1 - 1 = 0 index
                $current_index = $current_task['on_going_week'] - 1;
                $task_week_history[$current_index]['day'][] = $current_task['current_day'];
                $completedDaysForThisTask = count($task_week_history[$current_index]['day']);
                $temp = $this->getTotalTaskDay($taskId, $current_task['on_going_week']);
                if (empty($temp)) {
                    return Helpers::getResponse(404, "No Limit Calculation Found");
                }
                $total_day = $temp['total_day'];
                $y = $temp['y'];
                if ($total_day != 0)
                    $percentage = round($completedDaysForThisTask / $total_day * 100);
                else
                    $percentage = 0;
                //y - The minimum threshold value which concludes user has completed this week tasks
                if ($percentage >= $y) {
                    $task_week_history[$current_index]['week_done_state'] = 1;
                } else {
                    $task_week_history[$current_index]['week_done_state'] = 0;
                }
                $task_week_history[$current_index]['week_progress_percentage'] = $percentage;
                $current_task['task_week_history'] = $task_week_history;
                $current_task['last_date'] = $this->date->format("d-m-Y");
                $current_task['last_week'] = $this->this_week;
                $current_task['back_down_week'] = $this->this_week;
                if ($current_index == $total_weeks && $current_task['current_day'] == 7) {
                    Log::info("This task week is finally completed for $userObj");
                    $total_percentage = 0;
                    $all_done_state = true;
                    for ($i = 1; $i <= $current_index; $i++) {
                        $total_percentage += $task_week_history[$i]['week_progress_percentage'];
                        $all_done_state = $task_week_history[$i]['week_done_state'] == 0 ? false : true;
                    }
                    $current_task['total_progress_percentage'] = $total_percentage / $total_weeks;
                    if ($all_done_state) {
                        $current_task['task_complete_state'] = 1;
                    }
                    $current_task['task_done_state'] = 1;
                }
                $tasks[$foundIndex] = $current_task;
                $user_task->task = $tasks;
                if ($user_task->save()) {
                    if ($this->updateUserCategoryModuleScore($userObj, $taskLevel, $category)
                        && $this->updateAchievementsAndFeeds($userObj, $taskBank, $current_task['on_going_week'], $current_task['current_day'])) {
                        $categoryScore = strtolower($category) . "_score";
                        $dayMessage = weeklyTask::getDayCompleteMessage($current_task['current_day'], $current_task['on_going_week'], $taskBank->id);
                        $responseArr = [
                            'user_id' => $userObj->id,
                            'image' => url("api/task/$taskBank->id/week/" . $current_task['on_going_week'] . "/day/" . $current_task['current_day'] . "/image"),
                            'message' => $dayMessage,
                            'category_score' => $userObj->taskInformation->$categoryScore,
                            'overall_score' => $userObj->taskInformation->overall_score,
                        ];
                        $responseArr['weekImage'] = $current_task == 7 ?
                            url("api/task/$taskBank->id/week/" . $current_task['on_going_week'] . "/image") : null;
                        return Helpers::getResponse(200, "Task Completed", $responseArr);
                    }
                } else {
                    return Helpers::getResponse(500, "Failed to complete task");
                }
            } else {
                return Helpers::getResponse(404, "Sorry, you are not registered for this task");
            }
        } catch (UserNotFoundException $e) {
            return $e->sendUserNotFoundExceptionResponse();
        } catch (\Exception $e) {
            return Helpers::getResponse(500, "Exception Occurred", $e->getMessage());
        }
    }

    /**
     * Update the task week history object from selected $currentTask Object Array
     * @author  Mayank Jariwala
     * @param $current_task
     * @return mixed
     */
    private function updateTaskWeekHistory($current_task)
    {
        $task_week_history = $current_task['task_week_history'];
        if (!empty($task_week_history)) {
            try {
                $start_date = new \DateTime($current_task['start_date'], new DateTimeZone(env('DATETIME_ZONE')));
                $current_week = floor($start_date->diff($this->date)->days / 7) + 1;
                $this->days_difference = (int)($start_date->diff($this->date)->format("%a")) + 1;
                $current_task['current_day'] = $this->days_difference % 7 == 0 ? 7 : $this->days_difference % 7;
                $current_task['on_going_week'] = $current_week;
                $current_index = $current_task['on_going_week'];
                $task_week_history[$current_index]['week'] = $current_week;
            } catch (\Exception $e) {
                Log::error("Exception Occurred" . $e->getTraceAsString());
            }
        } else {
            $current_index = 0;
            $task_week_history[$current_index]['week'] = $current_task['on_going_week'];
            $current_task['task_week_history'] = $task_week_history;
            // First day of user of 1st week
            $current_task['current_day'] = 1;
        }
        return $current_task;
    }

    /**
     * Update User Specific Category Score and Overall Score
     * @param $userObj
     * @param $taskLevel
     * @param $category
     * @return bool
     */
    private function updateUserCategoryModuleScore($userObj, $taskLevel, $category)
    {
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
        } catch (\Exception $e) {
            Log::warning("Exception Occurred while updating user object", $e->getTrace());
            return false;
        }
        return true;
    }

    private function updateAchievementsAndFeeds($userObj, $taskBank, $weekNo, $day)
    {
        try {
            if ($day == 7) {
                $achievementData = [
                    [
                        'user_id' => $userObj->id,
                        'badge_url' => "task/$taskBank->id/week/$weekNo/day/$day/image"
                    ],
                    [
                        'user_id' => $userObj->id,
                        'badge_url' => "task/$taskBank->id/week/$weekNo/image"
                    ]
                ];
                UserAchievements::insert($achievementData);
            } else {
                UserAchievements::updateOrCreate([
                    'user_id' => $userObj->id,
                    'badge_url' => "task/$taskBank->id/week/$weekNo/day/$day/image"
                ]);
            }
            $this->feedsController->addUsersFeeds($userObj->id, $day, $weekNo, $taskBank->id);
        } catch (\Exception $e) {
            Log::warning("Exception Occurred while updating user achievements", $e->getTrace());
            return false;
        }
        return true;
    }

    /**
     * Annonymous developer
     * @version  1.0
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    // Deprecated- Developed by Other Developer
    public
    function taskUnregister(Request $request)
    {
        $task_id = $request->input('task_id');
        $taskBank = taskBank::find($task_id);
        $category = '';
        if (!is_null($taskBank))
            $category = $taskBank->getTaskCategory->name;

        $user = auth::user();
        $user_id = $user->id;

        $user_task = $this->getUserTask($user_id);
        if (!is_null($user_task)) {
            $task = $user_task->task;

            $DoingTasks = $user_task[$category . "_DoingTasks"];  // Will remove that task id from doing task array
            $doing_task_index = -1;

            $check = $this->checkIfDoingTask($user, $taskBank);
            $is_doing_task = $check['is_doing_task'];
            if ($is_doing_task != -1) {  //This means user is doing task or finished task
                $task_index = $check['task_index'];
                $task = $user_task->task;
                array_splice($task, $task_index, 1);
                array_splice($DoingTasks, $doing_task_index, 1);
                if (!empty($task)) {
                    $user_task->task = $task;
                    $columnName = $category . "_DoingTasks";
                    $user_task->$columnName = $DoingTasks;
                    $user_task->save();
                } else {
                    $user_task->delete();
                    $user_task = null;
                }
                $this->user_task = $user_task;
                $taskBank->registered_users = $taskBank->registered_users - 1;
                $taskBank->save();
                return response()->json(['user_task' => $user_task, 'task_index' => $task_index]);
            } else
                return null;
        } else {
            return null;
        }
    }

    /**
     * @author  Mayank Jariwala
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unregisterTask(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'userId' => 'required',
            'taskId' => 'required',
            'isMixedBag' => 'required'
        ]);
        if ($validation->fails()) {
            return Helpers::getResponse(404, "Validation Error", $validation->getMessageBag()->all());
        }
        $isMixedBag = $request->input('isMixedBag');
        if ($isMixedBag)
            return $this->mixedBagController->unregisterMixedTask($request);
        $task_id = $request->input('taskId');
        $user_id = $request->input('userId');
        try {
            return $this->processTaskUnregister($task_id, $user_id);
        } catch (\Exception $e) {
            return Helpers::getResponse(404, $e->getMessage());
        }
    }

    /**
     * This function simply checks that whether user doing such task and if yes then simply remove its record
     *
     * @version 1.0.1
     * @author  Mayank Jariwala
     * @param $task_id
     * @param $user_id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    private function processTaskUnregister($task_id, $user_id)
    {
        try {
            User::getUser($user_id);
            $taskBank = taskBank::find($task_id);
            if (!is_null($taskBank))
                $category = $taskBank->getTaskCategory->name;
            else
                throw new \Exception("No Task  Found");
            $user_task = $this->getUserTask($user_id);
            if (!is_null($user_task)) {
                $task = $user_task->task;
                $DoingTasks = $user_task[$category . "_DoingTasks"];
                if (!empty($DoingTasks) && in_array($task_id, $DoingTasks)) {
                    $keyIndex = array_search($task_id, $DoingTasks);
                    unset($DoingTasks[$keyIndex]);
                } else {
                    throw  new \Exception("User is not doing $taskBank->task_name task");
                }
                $taskKeyIndex = -1;
                foreach ($task as $key => $value) {
                    if ($value['id'] === $task_id) {
                        $taskKeyIndex = $key;
                        break;
                    }
                }
                if ($taskKeyIndex !== -1) {
                    unset($task[$taskKeyIndex]);
                    $columnName = $category . "_DoingTasks";
                    $user_task->$columnName = array_values($DoingTasks);
                    $user_task->task = array_values($task);
                    if (!$user_task->save()) {
                        throw new \Exception("Failed to unregistered task");
                    }
                    return Helpers::getResponse(200, "Task unregistered");
                } else {
                    throw  new \Exception("User is not doing $taskBank->task_name task");
                }
            }
            throw new \Exception("User is not doing any task");
        } catch (UserNotFoundException $e) {
            $e->setMessage("User not found");
            return $e->sendUserNotFoundExceptionResponse();
        }
    }

    /**
     * Get User Task Count doing in each category [ Max: 2 , Min : 0]
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserTasksCount($userId)
    {
        $userTask = $this->getUserTask($userId);
        $response = null;
        if ($userTask == null) {
            $status = "User haven't started any task";
        } else {
            $status = "Task count";
            $response = [
                'Physics_count' => $userTask->Physics_DoingTasks == null ? 0 : count($userTask->Physics_DoingTasks),
                'Mental_count' => $userTask->Mental_DoingTasks == null ? 0 : count($userTask->Mental_DoingTasks),
                'Nutrition_count' => $userTask->Nutrition_DoingTasks == null ? 0 : count($userTask->Nutrition_DoingTasks),
                'Lifestyle_count' => $userTask->Lifestyle_DoingTasks == null ? 0 : count($userTask->Lifestyle_DoingTasks)
            ];
        }
        return response()->json([
            'statusCode' => 200,
            'statusMessage' => $status,
            'response' => $response
        ]);
    }

    public function displayRegimenBadgeImage($id)
    {
        $taskBank = taskBank::getTaskBankObject($id);
        return response()
            ->make($taskBank->image)
            ->header('Content-Type', $taskBank->image_type);
    }

    /**
     * Display Image Stored Data
     * @param $taskBankId
     * @param $weekNo
     * @return \Illuminate\Http\Response
     */
    public function displayWeeklyBadgeImage($taskBankId, $weekNo)
    {
        $weeklyTask = weeklyTask::where('taskBank_id', $taskBankId)
            ->where('week', $weekNo)
            ->first(['image']);
        return response()
            ->make($weeklyTask->image)
            ->header('Content-Type', 'image/jpeg');
    }

    public function displayDailyBadgeImage($taskBankId, $weekNo, $day)
    {
        $adminTaskController = new TaskControllerV2();
        return $adminTaskController->loadTaskSpecificWeekDayBadge($taskBankId, $weekNo, $day);
    }

    private function _group_by($array, $key)
    {
        $return = array();
        foreach ($array as $val) {
            $return[$val[$key]][] = $val;
        }
        return $return;
    }
}
