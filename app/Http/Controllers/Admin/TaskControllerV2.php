<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\RegimenNotFoundException;
use App\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Model\Tasks\taskBank;
use App\Model\Tasks\weeklyTask;
use App\Respositories\CategoryRepository;
use App\Respositories\TaskBankRepository;
use App\Respositories\WeeklyTaskRepository;
use App\Services\TaskServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TaskControllerV2 extends Controller
{
    private $taskServices, $weekTaskRepo, $categoryRepo, $taskBankRepo;

    public function __construct(TaskServices $taskServices, WeeklyTaskRepository $weekTaskRepo, CategoryRepository $categoryRepo)
    {
        $this->taskServices = $taskServices;
        $this->weekTaskRepo = $weekTaskRepo;
        $this->categoryRepo = $categoryRepo;
        $this->taskBankRepo = new TaskBankRepository();
    }


    public function getWeeklyTaskAdvisePage($week, $code)
    {
        $totalWeeks = $this->taskServices->regimenWeekDetails($code)->count();
        $weekInfo = $this->taskServices->weekTaskObject($code, $week);
        return view("admin.tasks.advise", compact("week", "code", 'weekInfo', 'totalWeeks'));
    }

    public function getWeeklyTaskAdviseInfo($week, $code)
    {
        $weekInfo = $this->taskServices->weekTaskObject($code, $week);
        $advise = $weekInfo["advise"];
        return ["data" => $advise];
    }

    public function regimenPage(string $category)
    {
        $categoryObj = $this->categoryRepo->getCategoryInfo($category);
        $categoryId = $categoryObj->id;
        return view('admin.tasks.taskBankV2.taskBankView', compact('category', 'categoryId'));
    }

    public function getRegimenInfo($categoryName)
    {
        $category = $this->categoryRepo->getCategoryInfo($categoryName);
        return $this->taskServices->getCategoryRegimens($category->id);
    }

    public function getRegimenWeekDetailsPage($regimenCode)
    {
        try {
            $regimen = $this->taskServices->regimenByCode($regimenCode, ['id', "task_name"]);
            $regimenName = $regimen->task_name;
        } catch (RegimenNotFoundException $e) {
            return $e->sendRegimenNotFoundExceptionResponse();
        }
        return view('admin.tasks.weekTaskV2.weekTaskViewV2', compact('regimenName', 'regimenCode'));
    }

    public function getRegimenWeekDetailsInfo($regimenCode)
    {
        $weekly_tasks = $this->taskServices->regimenWeekDetails($regimenCode);
        $result = $this->getWeekTasksInfoArr($weekly_tasks);
        return response($result)->withHeaders([
            'Content-Type' => 'application/json',
        ]);
    }

    private function getWeekTasksInfoArr($weekly_tasks)
    {
        $result = Array();
        foreach ($weekly_tasks as $weekly_task) {
            $result[] = [
                'ID' => $weekly_task->id,
                'taskBank_id' => $weekly_task->taskBank_id,
                'week' => $weekly_task->week,
                'advise' => $weekly_task->advise,
                'day1_title' => $weekly_task->day1_title,
                'day1_message' => $weekly_task->day1_message,
                'day1_badge' => $weekly_task->day1_badge,
                'day2_title' => $weekly_task->day2_title,
                'day2_message' => $weekly_task->day2_message,
                'day2_badge' => $weekly_task->day2_badge,
                'day3_title' => $weekly_task->day3_title,
                'day3_message' => $weekly_task->day3_message,
                'day3_badge' => $weekly_task->day3_badge,
                'day4_title' => $weekly_task->day4_title,
                'day4_message' => $weekly_task->day4_message,
                'day4_badge' => $weekly_task->day4_badge,
                'day5_title' => $weekly_task->day5_title,
                'day5_message' => $weekly_task->day5_message,
                'day5_badge' => $weekly_task->day5_badge,
                'day6_title' => $weekly_task->day6_title,
                'day6_message' => $weekly_task->day6_message,
                'day6_badge' => $weekly_task->day6_badge,
                'day7_title' => $weekly_task->day7_title,
                'day7_message' => $weekly_task->day7_message,
                'day7_badge' => $weekly_task->day7_badge,
                'week_detail' => $weekly_task->week_detail,
                'image' => $weekly_task->image,
            ];
        }
        return $result;
    }

    public function updateWeeklyTaskAdviseInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "code" => "required",
            "week" => "required",
        ]);
        if ($validator->fails()) {
            return ["error" => $validator->getMessageBag()->first()];
        }
        try {
            $weekObject = $this->taskServices->weekTaskObject($request->get("code"), $request->get("week"));
            $weekObject->advise = $request->advise;
            $weekObject->save();
            return ["data" => "advise updated"];
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function getRegimenWeekInfoPage($regimenCode, $weekNo)
    {
        $total_weeks = $this->weekTaskRepo->getTaskTotalWeeks($regimenCode);
        try {
            $category = $this->taskServices->regimenByCode($regimenCode, ["id", "category"]);
            $categoryName = $category->getTaskCategory->name;
            $weeksCountArr = range(1, $total_weeks, 1);
            $taskInfo = $this->getRegimenWeeklyTaskInfo($regimenCode, $weekNo);
            return view('admin.tasks.weekTaskV2.editWeeklyTaskPage', compact('regimenCode',
                'weeksCountArr', 'categoryName', 'taskInfo', 'weekNo'));
        } catch (RegimenNotFoundException $e) {
            return $e->sendRegimenNotFoundExceptionResponse();
        }
    }

    public function getRegimenWeeklyTaskInfo($taskBankCode, $weekNo = 1)
    {
        $getTaskInfo = $this->weekTaskRepo->findWeekTaskByWeekNoAndCode($weekNo, $taskBankCode, [
            'week',
            'day1_title', 'day1_message', 'day1_badge',
            'day2_title', 'day2_message', 'day2_badge',
            'day3_title', 'day3_message', 'day3_badge',
            'day4_title', 'day4_message', 'day4_badge',
            'day5_title', 'day5_message', 'day5_badge',
            'day6_title', 'day6_message', 'day6_badge',
            'day7_title', 'day7_message', 'day7_badge'
        ]);
        $result = array();
        for ($i = 1; $i <= 7; $i++) {
            $dayTitleColumn = $getTaskInfo["day" . $i . "_title"];
            $dayMessageColumn = $getTaskInfo["day" . $i . "_message"];
            $result[] = [
                'day' => $i,
                'badge' => $getTaskInfo["day" . $i . "_badge"],
                'week' => $getTaskInfo['week'],
                'title' => $dayTitleColumn,
                'message' => $dayMessageColumn
            ];
        }
        return $result;
    }

    public function updateRegimenWeekObj(Request $request, $taskBankId, $weekNo)
    {
        $item = $request->get('item');
        $validation = Validator::make($item, [
            'advise' => 'required',
            'day1_title' => 'required',
            'day2_title' => 'required',
            'day3_title' => 'required',
            'day4_title' => 'required',
            'day5_title' => 'required',
            'day6_title' => 'required',
            'day7_title' => 'required',
        ]);
        if ($validation->fails())
            return Helpers::sendResponse(["error" => $validation->getMessageBag()->first()]);
        $dataToUpdate = array_except($item, ["ID", "taskBank_id", "week"]);
        try {
            $this->taskServices->updateRegimenWeek($taskBankId, $item["week"], $dataToUpdate);
            return Helpers::sendResponse(["data" => "Information Updated"]);
        } catch (\Exception $e) {
            return Helpers::sendResponse(["error" => $e->getMessage()]);
        }
    }

    public function uploadRegimenImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'regimenId' => 'required',
            'fileData' => 'required'
        ]);
        if ($validator->fails())
            return Helpers::sendResponse(["error" => $validator->getMessageBag()->first()]);
        $filePath = $request->file("fileData")->getRealPath();
        try {
            $fileType = pathinfo($_FILES['fileData']['name'], PATHINFO_EXTENSION);
            $this->taskServices->uploadRegimenBadge($request->regimenId, $fileType, $filePath);
            return Helpers::sendResponse(["data" => "Image Uploaded"]);
        } catch (RegimenNotFoundException $e) {
            return Helpers::sendResponse(["error" => $e->getMessage()]);
        }
    }

    public function uploadDailyBadge(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'regimenCode' => 'required',
            'week' => 'required',
            'day' => 'required'
        ]);
        if ($validation->fails())
            return Helpers::sendResponse(["error" => $validation->getMessageBag()->first()]);
        $taskBankId = $request->get('regimenCode');
        $week = $request->get('week');
        $day = $request->get('day');
        $filePath = $request->file("fileData")->getRealPath();
        try {
            $this->taskServices->uploadDailyBadge($taskBankId, $week, $day, $filePath);
            return Helpers::sendResponse(["data" => "Image Uploaded"]);
        } catch (\Exception $e) {
            return Helpers::sendResponse(["error" => $e->getMessage()]);
        }
    }

    public function insertRegimen(Request $request)
    {
        $taskBankObj = $request->get('item');
        $validator = Validator::make($taskBankObj, [
            'task_name' => 'required',
            'code' => 'required|unique:task_banks',
            'title' => 'required'
        ], [
            'code.unique' => 'Regimen code is already being in use'
        ]);
        if ($validator->fails()) {
            return ["error" => $validator->getMessageBag()->first()];
        }
        $this->taskServices->createNewRegimen($taskBankObj);
        return ["data" => "New Regimen Added"];
    }

    public function updateRegimen(Request $request)
    {
        $taskBankObj = $request->get('item');
        $id = $taskBankObj["ID"];
        $bankFieldToUpdate = array_except($taskBankObj, ['id']);
        try {
            $this->taskBankRepo->updateTaskBank($id, $bankFieldToUpdate);
            return ["data" => "Regimen Updated"];
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function deleteRegimen($regimenCode)
    {
        try {
            $this->taskServices->deleteRegimen($regimenCode);
            return ["data" => "Regimen Deleted"];
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function insertWeeklyRegimen(Request $request)
    {
        $weekDetails = $request->get("weekDetails");
        $validationArr = [];
        for ($i = 1; $i <= 7; $i++) {
            $validationArr["day$i" . "_title"] = "required";
        }
        $validator = Validator::make($weekDetails, $validationArr);
        if ($validator->fails())
            return ["error" => $validator->getMessageBag()->first()];
        $this->taskServices->addWeekTask($weekDetails);
        return ["data" => "New Weekly Task Added"];
    }

    public function deleteWeeklyRegimen($week, $code)
    {
        try {
            $this->taskServices->deleteWeekTask($week, $code);
            return ["data" => "Weekly Task Deleted"];
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
    // -----------------

//    [Need to update - Pending]
    public function uploadWeeklyTaskWeekImage($regimenCode, $weekNo)
    {
        $weeklyTask = $this->taskServices->weekTaskObject($regimenCode, $weekNo);
        try {
            $weeklyTask->image = file_get_contents($_FILES['image']['tmp_name']);
            if ($weeklyTask->save())
                return Helpers::getResponse(200, "Image Uploaded");
        } catch (\Exception $e) {
            return Helpers::getResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    // ----------------- TaskBank/Regimen CRUD Operation -------

    public function fetchTestNameFromTestId($regimenIdArr)
    {
        $testNameArr = $this->taskBankRepo->whereIn('id', $regimenIdArr)->pluck('title')->toArray();
        return array_unique($testNameArr);
    }


    public function writeToFileTaskDoneMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "message" => "required"
        ]);
        if ($validator->fails())
            return ["error" => $validator->getMessageBag()->first()];
        $message = trim($request->get("message"));
        try {
            Storage::disk("rootDir")->put("task-complete-message.txt", $message);
            return ["data" => "Message saved"];
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }


    // ----------------- TaskBank/Regimen CRUD Operation END-------

//    Flush Old And Upload New Tasks Services logic
    public function flushAndUploadNewRegimenData()
    {
        $regimenFile = file_get_contents($_FILES['regimen']['tmp_name']);
        $weeklyTaskFile = file_get_contents($_FILES['weekly_task']['tmp_name']);
        $regimenFileContents = explode("\n", $regimenFile);
        $weeklyTaskFileContents = explode("\n", $weeklyTaskFile);
        $regimenColumnNameArr = explode(",", trim($regimenFileContents[0]));
        $weeklyTaskColumnNameArr = explode(",", trim($weeklyTaskFileContents[0]));
        $columnValidationStatus = $this->columnValidationOfFiles($regimenColumnNameArr, $weeklyTaskColumnNameArr);
        if (!$columnValidationStatus)
            return Helpers::getResponse(400, "Files Column names didn't match");
        try {
            $getAllNewDataOfRegimen = $this->readCsvDataAndMapToColumn($regimenColumnNameArr, $regimenFileContents);
            $getAllNewDataOfWeeklyTask = $this->readCsvDataAndMapToColumn($weeklyTaskColumnNameArr, $weeklyTaskFileContents);
            $bankAddResponse = $this->truncateAndAddNewData("task_banks", $getAllNewDataOfRegimen);
            $weekAddResponse = $this->truncateAndAddNewData("weekly_tasks", $getAllNewDataOfWeeklyTask);
            if ($bankAddResponse && $weekAddResponse) {
                return Helpers::getResponse(200, "New Data Uploaded");
            } else {
                return Helpers::getResponse(400, "New Data failed to upload");
            }
        } catch (\Exception $e) {
            return Helpers::getResponse(400, $e->getMessage());
        }
    }

    private function columnValidationOfFiles($regimenFileColumn, $weeklyTaskFileColumn)
    {
        $databaseColumnOfRegimen = ['id', 'task_name', 'level', 'step', 'detail', 'title', 'category', 'image', 'image_type', 'registered_users'];
        $databaseColumnOfWeeklyTask = ['taskBank_id', 'week', 'day1_title', 'day1_message', 'day1_badge',
            'day2_title', 'day2_message', 'day2_badge', 'day3_title', 'day3_message', 'day3_badge',
            'day4_title', 'day4_message', 'day4_badge', 'day5_title', 'day5_message', 'day5_badge',
            'day6_title', 'day6_message', 'day6_badge', 'day7_title', 'day7_message', 'day7_badge',
            'image', 'week_detail', 'x', 'y', 'badge'];
        $diffColumnCountOfRegimen = count(array_diff($regimenFileColumn, $databaseColumnOfRegimen));
        $diffColumnCountOfWeeklyTask = count(array_diff($weeklyTaskFileColumn, $databaseColumnOfWeeklyTask));
        return $diffColumnCountOfRegimen == 0 && $diffColumnCountOfWeeklyTask == 0;
    }

    private function readCsvDataAndMapToColumn(array $regimenColumnNameArr, array $regimenFileContents)
    {
        $finalData = [];
        for ($i = 1; $i < count($regimenFileContents); $i++) {
            $rowData = str_getcsv($regimenFileContents[$i], ',', '"');
            $data = [];
            if (!empty($rowData)) {
                for ($j = 0; $j < count($rowData); $j++) {
                    // if id is 0 , it means this row is useless as per custom rules
                    if (!empty(trim($rowData[0]))) {
                        $data += [
                            $regimenColumnNameArr[$j] => $rowData[$j]
                        ];
                    }
                }
                if (!empty($data))
                    array_push($finalData, $data);
            }
        }
        return $finalData;
    }

    private function truncateAndAddNewData(string $tableName, array $newData)
    {
        if ($tableName == "task_banks") {
            $this->taskBankRepo->query()->truncate();
            $this->taskBankRepo->insert($newData);
            return true;
        } else if ($tableName == "weekly_tasks") {
            $this->weekTaskRepo->query()->truncate();
            $this->weekTaskRepo->insert($newData);
            return true;
        }
        return false;
    }
}
