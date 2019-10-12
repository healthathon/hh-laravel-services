<?php

namespace App\Http\Controllers\Api;

use App\Helpers;
use App\Http\Controllers\Controller;
use App\Model\User;
use Illuminate\Support\Collection;

/**
 * Class LeaderBoardController  : All Operations related to leaders
 * @package App\Http\Controllers\Api
 */
class LeaderBoardController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTop20Users()
    {
        $users = User::with('taskInformation:user_id,overall_score')
            ->limit(20)
            ->get(['id', 'first_name', 'last_name'])
            ->sortByDesc('taskInformation.overall_score');
        $response = [];
        foreach ($users as $user) {
            $user["profile_pic"] = url("api/user/$user->id/get-profile-image");
            $response[] = $user;
        }
        return Helpers::getResponse(200, "Top 20 Leaders", $response);
    }
}
