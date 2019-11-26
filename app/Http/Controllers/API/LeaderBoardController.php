<?php

namespace App\Http\Controllers\Api;

use App\Helpers;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\UsersTaskInformations;
use App\Respositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class LeaderBoardController  : All Operations related to leaders
 * @package App\Http\Controllers\Api
 */
class LeaderBoardController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getTop20Users()
    {
        try {
            $taskColumnToFetch = "taskInformation:user_id,overall_score,physical_task_completed," .
                "mental_task_completed,nutrition_task_completed,lifestyle_task_completed";
            $users = (new UserRepository())->with($taskColumnToFetch)
                ->limit(20)
                ->get(['id', 'first_name', 'last_name'])
                ->sortByDesc('taskInformation.overall_score');
            $response = [];
            // Bad Logic for Rank : temp fixes
            $rank = 1;
            foreach ($users as $user) {
                $user["rank"] = $rank;
                if ($user->taskInformation != null) {
                    $user['task_information'] = [
                        'aa' => "ssdsd"
                    ];
                    $userTaskInfo = $user->taskInformation->toArray();
                    $data = array_except($userTaskInfo, ['overall_score', 'user_id']);
                    $user->taskInformation->total_task_completed = $this->getTotalTaskCompleted($data);
                }
                $user["profile_pic"] = url("api/user/$user->id/get-profile-image");
                $rank++;
                $response[] = $user;
            }
            return Helpers::getResponse(200, "Top 20 Leaders", $response);
        } catch (\Exception $e) {
            return Helpers::getResponse(500, "Server Error", $e->getMessage());
        }
    }

    private function getTotalTaskCompleted(array $categoryTaskCompletedColumns)
    {
        $sum = 0;
        foreach ($categoryTaskCompletedColumns as $categoryTaskCompletedColumn) {
            $sum += $categoryTaskCompletedColumn;
        }
        return $sum;
    }
}
