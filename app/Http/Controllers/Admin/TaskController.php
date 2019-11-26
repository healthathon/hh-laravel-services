<?php

namespace App\Http\Controllers\Admin;

use App\Respositories\TaskBankRepository;
use App\Respositories\WeeklyTaskRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Tasks\taskBank;
use App\Model\Tasks\weeklyTask;

define('PHYSICS_ID', 1);
define('MENTAL_ID', 2);
define('NUTRITION_ID', 3);
define('LIFESTYLE_ID', 4);

/**
 * NOTE : This Controller functions are deprecated as most of functions are defined
 * unnecessarily : The reason for keeping this class is to review the previous api running
 * previously
 *
 * Class TaskController
 * @author  Annonymous Developer
 * @package App\Http\Controllers\Admin
 */
class TaskController extends Controller
{
    protected $taskBankRepo, $weeklyTaskRepo;

    public function __construct()
    {
        $this->taskBankRepo = new TaskBankRepository();
        $this->weeklyTaskRepo = new WeeklyTaskRepository();
    }

    public function showPhysicsBank()
    {
        return view('admin.tasks.__taskBank.physicsBank');
    }

    public function getPhysicsBank()
    {
        $taskBanks = $this->taskBankRepo->where('category', PHYSICS_ID)->get();
        $result = Array();
        $i = 0;
        foreach ($taskBanks as $taskBank) {
            $result[$i]['ID'] = $taskBank->id;
            $result[$i]['task_name'] = $taskBank->task_name;
            $result[$i]['level'] = (int)$taskBank->level;
            $result[$i]['step'] = (int)$taskBank->step;
            $result[$i]['view_badge'] = url("admin/regimen/$taskBank->id/image");
            $result[$i]['regimen_badge'] = " <input type = 'file' name = 'image' onchange = 'showContent(this, $taskBank->id)' accept = 'image/*' />";
            $result[$i]['detail'] = $taskBank->detail;
            $result[$i]['title'] = $taskBank->title;
            $i++;
        }
        return response($result)->withHeaders([
            'Content-Type' => 'application/json',
        ]);
    }

    public function insertPhysicsBank(Request $request)
    {
        $item = $request->input('item');
        $taskBank = new taskBank;
        $taskBank->task_name = $item['task_name'];
        $taskBank->level = $item['level'];
        $taskBank->step = $item['step'];
        $taskBank->detail = $item['detail'];
        $taskBank->title = $item['title'];

        $taskBank->category = 'Physics';
        $taskBank->save();
        $item['ID'] = (int)$taskBank->id;
        $item['level'] = (int)$item['level'];
        $item['step'] = (int)$item['step'];

        return response($item)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

    public function updatePhysicsBank(Request $request)
    {
        $item = $request->input('item');
        $id = $item['ID'];
        $taskBank = $this->taskBankRepo->find($id);
        $taskBank->task_name = $item['task_name'];
        $taskBank->level = $item['level'];
        $taskBank->step = $item['step'];
        $taskBank->detail = $item['detail'];
        $taskBank->title = $item['title'];
        $taskBank->save();
        $item['ID'] = (int)$taskBank->id;
        $item['level'] = (int)$item['level'];
        $item['step'] = (int)$item['step'];

        return response($item)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

    public function deletePhysicsBank(Request $request)
    {
        $item = $request->input('item');
        $id = $item['ID'];
        $taskBank = $this->taskBankRepo->find($id);
        $taskBank->task_name = $item['task_name'];
        $taskBank->level = $item['level'];
        $taskBank->step = $item['step'];
        $taskBank->detail = $item['detail'];
        $taskBank->title = $item['title'];
        $taskBank->delete();
    }

//          <--------- End Physical ------------->


//          <------------ Mental Bank ------------->

    public function showMentalBank()
    {
        return view('admin.tasks.__taskBank.mentalBank');
    }

    public function getMentalBank()
    {
        $taskBanks = $this->taskBankRepo->where('category', MENTAL_ID)->get();
        $result = Array();
        $i = 0;
        foreach ($taskBanks as $taskBank) {
            $result[$i]['ID'] = $taskBank->id;
            $result[$i]['task_name'] = $taskBank->task_name;
            $result[$i]['level'] = (int)$taskBank->level;
            $result[$i]['view_badge'] = url("admin/regimen/$taskBank->id/image");
            $result[$i]['regimen_badge'] = " <input type = 'file' name = 'image'
            onchange = 'showContent(this, $taskBank->id)'
            accept = 'image/*' />";
            $result[$i]['detail'] = $taskBank->detail;
            $result[$i]['title'] = $taskBank->title;
            $i++;
        }
        return response($result)->withHeaders([
            'Content-Type' => 'application/json',
        ]);
    }

    public function insertMentalBank(Request $request)
    {
        $item = $request->input('item');
        $taskBank = new taskBank;
        $taskBank->task_name = $item['task_name'];
        $taskBank->level = $item['level'];
//                        $taskBank->detail=$item['detail'];
        $taskBank->title = $item['title'];

        $taskBank->category = 'Mental';
        $taskBank->save();
        $item['ID'] = (int)$taskBank->id;
        $item['level'] = (int)$item['level'];


        return response($item)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

    public function updateMentalBank(Request $request)
    {
        $item = $request->input('item');
        $id = $item['ID'];
        $taskBank = $this->taskBankRepo->find($id);
        $taskBank->task_name = $item['task_name'];
        $taskBank->level = $item['level'];
//                        $taskBank->detail=$item['detail'];
        $taskBank->title = $item['title'];
        $taskBank->save();
        $item['ID'] = (int)$taskBank->id;
        $item['level'] = (int)$item['level'];
        $item['step'] = (int)$item['step'];

        return response($item)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

    public function deleteMentalBank(Request $request)
    {
        $item = $request->input('item');
        $id = $item['ID'];
        $taskBank = $this->taskBankRepo->find($id);
        $taskBank->delete();
    }

//          <--------- End Mental ------------->


//          <------------ Lifestyle Bank ------------->

    public function showLifestyleBank()
    {
        return view('admin.tasks.__taskBank.lifestyleBank');
    }

    public function getLifestyleBank()
    {
        $taskBanks = $this->taskBankRepo->where('category', LIFESTYLE_ID)->get();
        $result = Array();
        $i = 0;
        foreach ($taskBanks as $taskBank) {
            $result[$i]['ID'] = $taskBank->id;
            $result[$i]['task_name'] = $taskBank->task_name;
            $result[$i]['title'] = $taskBank->title;
            $result[$i]['view_badge'] = url("admin/regimen/$taskBank->id/image");
            $result[$i]['regimen_badge'] = " <input type = 'file' name = 'image'
            onchange = 'showContent(this, $taskBank->id)'
            accept = 'image/*' />";
            $result[$i]['level'] = $taskBank->level;
            $i++;
        }
        return response($result)->withHeaders([
            'Content-Type' => 'application/json',
        ]);
    }

    public function insertLifestyleBank(Request $request)
    {
        $item = $request->input('item');
        $taskBank = new taskBank;
        $taskBank->task_name = $item['task_name'];
        $taskBank->title = $item['title'];
        $taskBank->category = 'Lifestyle';
        $taskBank->save();
        $item['ID'] = (int)$taskBank->id;
        return response($item)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

    public function updateLifestyleBank(Request $request)
    {
        $item = $request->input('item');
        $id = $item['ID'];
        $taskBank = $this->taskBankRepo->find($id);
        $taskBank->task_name = $item['task_name'];

        $taskBank->title = $item['title'];
        $taskBank->save();
        $item['ID'] = (int)$taskBank->id;
        return response($item)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

    public function deleteLifestyleBank(Request $request)
    {
        $item = $request->input('item');
        $id = $item['ID'];
        $taskBank = $this->taskBankRepo->find($id);
        $taskBank->delete();
    }

//          <--------- End Lifestyle ------------->


//          <------------ Nutrition Bank ------------->

    public function showNutritionBank()
    {
        return view('admin.tasks.__taskBank.nutritionBank');
    }

    public function getNutritionBank()
    {
        $taskBanks = $this->taskBankRepo->where('category', NUTRITION_ID)->get();
        $result = Array();
        $i = 0;
        foreach ($taskBanks as $taskBank) {
            $result[$i]['ID'] = $taskBank->id;
            $result[$i]['task_name'] = $taskBank->task_name;
            $result[$i]['view_badge'] = url("admin/regimen/$taskBank->id/image");
            $result[$i]['regimen_badge'] = " <input type = 'file' name = 'image' onchange = 'showContent(this, $taskBank->id)' accept = 'image/*' />";
            $result[$i]['title'] = $taskBank->title;
            $i++;
        }
        return response($result)->withHeaders([
            'Content-Type' => 'application/json',
        ]);
    }

    public function insertNutritionBank(Request $request)
    {
        $item = $request->input('item');
        $taskBank = new taskBank;
        $taskBank->task_name = $item['task_name'];
        $taskBank->title = $item['title'];
        $taskBank->category = 'Nutrition';
        $taskBank->save();
        $item['ID'] = (int)$taskBank->id;
        return response($item)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

    public function updateNutritionBank(Request $request)
    {
        $item = $request->input('item');
        $id = $item['ID'];
        $taskBank = $this->taskBankRepo->find($id);
        $taskBank->task_name = $item['task_name'];

        $taskBank->title = $item['title'];
        $taskBank->save();
        $item['ID'] = (int)$taskBank->id;
        return response($item)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

    public function deleteNutritionBank(Request $request)
    {
        $item = $request->input('item');
        $id = $item['ID'];
        $taskBank = $this->taskBankRepo->find($id);
        $taskBank->delete();
    }

//          <--------- End Nutrition ------------->

//   <-------------------------------------------------- End TaskBank ----------------------------------------------->


// <--------------------------------------------------- Weekly Task ------------------------------------------------>

//          <---------- Physical --------------->
    public function insertPhysicsWeekTask(Request $request)
    {
        $taskBank_id = (int)$request->input('taskBank_id');


        $taskWeek = new weeklyTask;
        $taskWeek->week = $request->input('week');
        $taskWeek->taskBank_id = $taskBank_id;
        $taskWeek->day1_title = $request->input('day1_title');
        $taskWeek->day2_title = $request->input('day2_title');
        $taskWeek->day3_title = $request->input('day3_title');
        $taskWeek->day4_title = $request->input('day4_title');
        $taskWeek->day5_title = $request->input('day5_title');
        $taskWeek->day6_title = $request->input('day6_title');
        $taskWeek->day7_title = $request->input('day7_title');
        $taskWeek->week_detail = $request->input('week_detail');

        $item = Array();

        $item['week'] = $request->input('week');
        $item['day1_title'] = $request->input('day1_title');
        $item['day2_title'] = $request->input('day2_title');
        $item['day3_title'] = $request->input('day3_title');
        $item['day4_title'] = $request->input('day4_title');
        $item['day5_title'] = $request->input('day5_title');
        $item['day6_title'] = $request->input('day6_title');
        $item['day7_title'] = $request->input('day7_title');
        $item['week_detail'] = $request->input('week_detail');

        if ($request->hasFile('badge')) {
            $file = $request->file('badge');
            $file->move(public_path() . '/badges', $file->getClientOriginalName());
            $taskWeek->badge = $file->getClientOriginalName();
            $item['badge'] = url(" / badges / " . $file->getClientOriginalName());

        }
        $taskWeek->save();
        $item['ID'] = $taskWeek->id;

        return response($item)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

    public function updatePhysicsWeekTask(Request $request)
    {
        $taskBank_id = (int)$request->input('taskBank_id');
        $id = $request->input('ID');
        $taskWeek = $this->weeklyTaskRepo->find($id);
        $taskWeek->week = $request->input('week');
        $taskWeek->taskBank_id = $taskBank_id;
        $taskWeek->day1_title = $request->input('day1_title');
        $taskWeek->day1_message = $request->input('day1_message');
        $taskWeek->day2_title = $request->input('day2_title');
        $taskWeek->day2_message = $request->input('day2_message');
        $taskWeek->day3_title = $request->input('day3_title');
        $taskWeek->day3_message = $request->input('day3_message');
        $taskWeek->day4_title = $request->input('day4_title');
        $taskWeek->day4_message = $request->input('day4_message');
        $taskWeek->day5_title = $request->input('day5_title');
        $taskWeek->day5_message = $request->input('day5_message');
        $taskWeek->day6_title = $request->input('day6_title');
        $taskWeek->day6_message = $request->input('day6_message');
        $taskWeek->day7_title = $request->input('day7_title');
        $taskWeek->day7_message = $request->input('day7_message');
        $item = Array();
        if ($taskWeek->save())
            return response($item)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ]);
        return response([])->withHeaders(['Content-Type' => 'application/json']);
    }

    public function deletePhysicsWeekTask(Request $request)
    {
        $item = $request->input('item');
        $id = $item['ID'];
        $taskWeek = $this->weeklyTaskRepo->find($id);
        $taskWeek->delete();
    }

//            <------------------------  End Physics  --------------------->


//          <---------- Mental --------------->

    public function showMentalWeekTask($taskBank_id)
    {
        return view('admin.tasks.weekTask.mentalWeekTask', compact('taskBank_id'));
    }

    public function getMentalWeekTask($taskBank_id)
    {
        $weekly_tasks = $this->weeklyTaskRepo->where('taskBank_id', $taskBank_id)->orderBy('week')->get();
        $result = Array();
        $i = 0;
        foreach ($weekly_tasks as $weekly_task) {
            $result[$i]['week'] = $weekly_task->week;
            $result[$i]['week_title'] = $weekly_task->day1_title;
            $result[$i]['y'] = $weekly_task->y;
            $result[$i]['x'] = $weekly_task->x;
            $result[$i]['week_detail'] = $weekly_task->week_detail;
            $result[$i]['ID'] = $weekly_task->id;
            if (!is_null($weekly_task->badge))
                $result[$i]['badge'] = url("public/badges / " . $weekly_task->badge);
            else
                $result[$i]['badge'] = null;
            $i++;
        }
        return response($result)->withHeaders([
            'Content-Type' => 'application/json',
        ]);

    }

    public function insertMentalWeekTask(Request $request)
    {
        $taskBank_id = (int)$request->input('taskBank_id');

        $taskWeek = new weeklyTask;
        $taskWeek->week = $request->input('week');
        $taskWeek->taskBank_id = $taskBank_id;

        $taskWeek->day1_title = $request->input('week_title');
        $taskWeek->day2_title = $request->input('week_title');
        $taskWeek->day3_title = $request->input('week_title');
        $taskWeek->day4_title = $request->input('week_title');
        $taskWeek->day5_title = $request->input('week_title');
        $taskWeek->day6_title = $request->input('week_title');
        $taskWeek->day7_title = "Rest";
        $taskWeek->week_detail = $request->input('week_detail');
        $taskWeek->y = $request->input('y');
        $taskWeek->x = $request->input('x');

        if ($request->hasFile('badge')) {
            $file = $request->file('badge');
            $file->move(public_path() . '/badges', $file->getClientOriginalName());
            $taskWeek->badge = $file->getClientOriginalName();
        }

        $item = Array();

        $item['week'] = $request->input('week');
        $item['week_title'] = $request->input('week_title');

        $item['y'] = (float)$request->input('y');
        $item['x'] = (int)$request->input('x');

        $item['week_detail'] = $request->input('week_detail');
        if (!is_null($taskWeek->badge))
            $item['badge'] = url("public/badges / " . $taskWeek->badge);
        $taskWeek->save();

        $item['ID'] = $taskWeek->id;

        return response($item)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

    public function updateMentalWeekTask(Request $request)
    {

        $taskBank_id = (int)$request->input('taskBank_id');
        $id = $request->input('ID');
        $taskWeek = $this->weeklyTaskRepo->find($id);
        $taskWeek->week = $request->input('week');
        $taskWeek->taskBank_id = $taskBank_id;


        $taskWeek->day1_title = $request->input('week_title');
        $taskWeek->day2_title = $request->input('week_title');
        $taskWeek->day3_title = $request->input('week_title');
        $taskWeek->day4_title = $request->input('week_title');
        $taskWeek->day5_title = $request->input('week_title');
        $taskWeek->day6_title = $request->input('week_title');
        $taskWeek->day7_title = "Rest";
        $taskWeek->week_detail = $request->input('week_detail');
        $taskWeek->y = $request->input('y');
        $taskWeek->x = $request->input('x');

        if ($request->hasFile('badge')) {
            $file = $request->file('badge');
            $file->move(public_path() . '/badges', $file->getClientOriginalName());
            $taskWeek->badge = $file->getClientOriginalName();
        }

        $item = Array();
        $item['ID'] = $request->input('ID');
        $item['week'] = $request->input('week');
        $item['week_title'] = $request->input('week_title');

        $item['y'] = (float)$request->input('y');
        $item['x'] = (int)$request->input('x');

        $item['week_detail'] = $request->input('week_detail');
        if (!is_null($taskWeek->badge))
            $item['badge'] = url("public/badges / " . $taskWeek->badge);
        $taskWeek->save();

        return response($item)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);

    }

    public function deleteMentalWeekTask(Request $request)
    {
        $item = $request->input('item');
        $id = $item['ID'];
        $taskWeek = $this->weeklyTaskRepo->find($id);
        $taskWeek->delete();
    }

//            <------------------------  End Mental  --------------------->


//          <---------- Lifestyle --------------->

    public function showLifestyleWeekTask($taskBank_id)
    {
        return view('admin.tasks.weekTask.lifestyleWeekTask', compact('taskBank_id'));
    }

    public function getLifestyleWeekTask($taskBank_id)
    {
        $weekly_tasks = $this->weeklyTaskRepo->where('taskBank_id', $taskBank_id)->orderBy('week')->get();
        $result = Array();
        $i = 0;
        foreach ($weekly_tasks as $weekly_task) {
            $result[$i]['week'] = $weekly_task->week;
            $result[$i]['week_title'] = $weekly_task->day1_title;
            $result[$i]['y'] = $weekly_task->y;
            $result[$i]['x'] = $weekly_task->x;
            $result[$i]['week_detail'] = $weekly_task->week_detail;
            $result[$i]['ID'] = $weekly_task->id;
            $i++;
        }
        return response($result)->withHeaders([
            'Content-Type' => 'application/json',
        ]);

    }

    public function insertLifestyleWeekTask(Request $request)
    {
        $taskBank_id = $request->input('taskBank_id');
        $item = $request->input('item');
        $taskWeek = new weeklyTask;
        $taskWeek->week = $item['week'];
        $taskWeek->taskBank_id = $taskBank_id;
        $taskWeek->day1_title = $item['week_title'];
        $taskWeek->day2_title = $item['week_title'];
        $taskWeek->day3_title = $item['week_title'];
        $taskWeek->day4_title = $item['week_title'];
        $taskWeek->day5_title = $item['week_title'];
        $taskWeek->day6_title = $item['week_title'];
        $taskWeek->day7_title = 'Rest';
        $taskWeek->y = $item['y'];
        $taskWeek->x = $item['x'];
        $taskWeek->week_detail = $item['week_detail'];
        $item['y'] = (float)$item['y'];
        $item['x'] = (int)$item['x'];

        $taskWeek->save();
        $item['ID'] = $taskWeek->id;

        return response($item)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

    public function updateLifestyleWeekTask(Request $request)
    {
        $taskBank_id = $request->input('taskBank_id');
        $item = $request->input('item');
        $id = $item['ID'];
        $taskWeek = $this->weeklyTaskRepo->find($id);
        $taskWeek->week = $item['week'];
        $taskWeek->taskBank_id = $taskBank_id;
        $taskWeek->day1_title = $item['week_title'];
        $taskWeek->day2_title = $item['week_title'];
        $taskWeek->day3_title = $item['week_title'];
        $taskWeek->day4_title = $item['week_title'];
        $taskWeek->day5_title = $item['week_title'];
        $taskWeek->day6_title = $item['week_title'];
        $taskWeek->day7_title = 'Rest';
        $taskWeek->y = $item['y'];
        $taskWeek->x = $item['x'];
        $taskWeek->week_detail = $item['week_detail'];
        $taskWeek->save();
        $item['ID'] = $taskWeek->id;
        $item['y'] = (float)$item['y'];
        $item['x'] = (int)$item['x'];

        return response($item)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

    public function deleteLifestyleWeekTask(Request $request)
    {
        $item = $request->input('item');
        $id = $item['ID'];
        $taskWeek = $this->weeklyTaskRepo->find($id);
        $taskWeek->delete();
    }

//            <------------------------  End Lifestyle  --------------------->


//          <---------- Nutrition --------------->

    public function showNutritionWeekTask($taskBank_id)
    {
        return view('admin.tasks.weekTask.nutritionWeekTask', compact('taskBank_id'));
    }

    public function getNutritionWeekTask($taskBank_id)
    {
        $weekly_tasks = $this->weeklyTaskRepo->where('taskBank_id', $taskBank_id)->orderBy('week')->get();
        $result = Array();
        $i = 0;
        foreach ($weekly_tasks as $weekly_task) {
            $result[$i]['week'] = $weekly_task->week;
            $result[$i]['week_title'] = $weekly_task->day1_title;
            $result[$i]['y'] = $weekly_task->y;
            $result[$i]['x'] = $weekly_task->x;
            $result[$i]['week_detail'] = $weekly_task->week_detail;
            $result[$i]['ID'] = $weekly_task->id;
            if (!is_null($weekly_task->badge))
                $result[$i]['badge'] = url("public/badges / " . $weekly_task->badge);
            else
                $result[$i]['badge'] = null;
            $i++;
        }
        return response($result)->withHeaders([
            'Content-Type' => 'application/json',
        ]);

    }

    public function insertNutritionWeekTask(Request $request)
    {
        $taskBank_id = (int)$request->input('taskBank_id');

        $taskWeek = new weeklyTask;
        $taskWeek->week = $request->input('week');
        $taskWeek->taskBank_id = $taskBank_id;

        $taskWeek->day1_title = $request->input('week_title');
        $taskWeek->day2_title = $request->input('week_title');
        $taskWeek->day3_title = $request->input('week_title');
        $taskWeek->day4_title = $request->input('week_title');
        $taskWeek->day5_title = $request->input('week_title');
        $taskWeek->day6_title = $request->input('week_title');
        $taskWeek->day7_title = "Rest";
        $taskWeek->week_detail = $request->input('week_detail');
        $taskWeek->y = $request->input('y');
        $taskWeek->x = $request->input('x');

        if ($request->hasFile('badge')) {
            $file = $request->file('badge');
            $file->move(public_path() . '/badges', $file->getClientOriginalName());
            $taskWeek->badge = $file->getClientOriginalName();
        }

        $item = Array();

        $item['week'] = $request->input('week');
        $item['week_title'] = $request->input('week_title');

        $item['y'] = (float)$request->input('y');
        $item['x'] = (int)$request->input('x');

        $item['week_detail'] = $request->input('week_detail');
        if (!is_null($taskWeek->badge))
            $item['badge'] = url("public/badges / " . $taskWeek->badge);
        $taskWeek->save();

        $item['ID'] = $taskWeek->id;

        return response($item)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

    public function updateNutritionWeekTask(Request $request)
    {
        $taskBank_id = (int)$request->input('taskBank_id');
        $id = $request->input('ID');
        $taskWeek = $this->weeklyTaskRepo->find($id);
        $taskWeek->week = $request->input('week');
        $taskWeek->taskBank_id = $taskBank_id;


        $taskWeek->day1_title = $request->input('week_title');
        $taskWeek->day2_title = $request->input('week_title');
        $taskWeek->day3_title = $request->input('week_title');
        $taskWeek->day4_title = $request->input('week_title');
        $taskWeek->day5_title = $request->input('week_title');
        $taskWeek->day6_title = $request->input('week_title');
        $taskWeek->day7_title = "Rest";
        $taskWeek->week_detail = $request->input('week_detail');
        $taskWeek->y = $request->input('y');
        $taskWeek->x = $request->input('x');

        if ($request->hasFile('badge')) {
            $file = $request->file('badge');
            $file->move(public_path() . '/badges', $file->getClientOriginalName());
            $taskWeek->badge = $file->getClientOriginalName();
        }

        $item = Array();
        $item['ID'] = $request->input('ID');
        $item['week'] = $request->input('week');
        $item['week_title'] = $request->input('week_title');

        $item['y'] = (float)$request->input('y');
        $item['x'] = (int)$request->input('x');

        $item['week_detail'] = $request->input('week_detail');
        if (!is_null($taskWeek->badge))
            $item['badge'] = url("public/badges / " . $taskWeek->badge);
        $taskWeek->save();

        return response($item)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);

    }

    public function deleteNutritionWeekTask(Request $request)
    {
        $item = $request->input('item');
        $id = $item['ID'];
        $taskWeek = $this->weeklyTaskRepo->find($id);
        $taskWeek->delete();
    }

//            <------------------------  End Nutrition  --------------------->
}
