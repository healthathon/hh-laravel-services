<?php

namespace App\Http\Controllers\Admin;

use App\Model\Tasks\taskBank;
use App\Model\User;
use App\Model\UserTask;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserTasksController extends Controller
{

    public function showUsersTasks()
    {
        return view('admin.userTasks');
    }

    public function getUsersTasks()
    {
        $users = User::all();
        $responseArr = [];
        $i = 0;
        foreach ($users as $user) {
            $doingTasks = $user->doingTask()->get();
            $responseArr[$i] = [
                "userId" => $user->id,
                "userName" => $user->first_name . " " . $user->last_name,
                "physical" => [],
                "nutrition" => [],
                "mental" => [],
                "lifestyle" => []
            ];
            if (count($doingTasks) > 0) {
                foreach ($doingTasks as $doingTask) {
                    $categoryName = strtolower($doingTask->regimenInfo->getTaskCategory->name);
                    $categoryName = $categoryName === "physics" ? "physical" : $categoryName;
                    array_push($responseArr[$i][$categoryName], $doingTask->regimenInfo->task_name);
                    $responseArr[$i][$categoryName] = [
                        implode(",", $responseArr[$i][$categoryName])
                    ];
                }
            }
            $i++;
        }
        return response($responseArr)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

    private function getTaskNameFromId($id)
    {
        $task = taskBank::where('id', $id)->first();
        return $task->task_name;
    }
}
