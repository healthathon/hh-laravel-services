<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\UserNotFoundException;
use App\Helpers;
use App\Model\Feeds;
use App\Model\Tasks\taskBank;
use App\Model\Tasks\weeklyTask;
use App\Model\User;
use App\Model\UserFriends;
use App\Respositories\UserRepository;
use App\Services\FeedsService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FeedsController extends Controller
{

    private $feedsService, $userRepo;

    public function __construct()
    {
        $this->feedsService = new FeedsService();
        $this->userRepo = new UserRepository();
    }

    public function addUsersFeeds($user_id, $day, $week_number, $taskBankId)
    {
        return $this->feedsService->addUsersFeeds($user_id, $day, $week_number, $taskBankId);
    }

    // Mayank Jariwala
    public function getFriendsFeeds($userId)
    {
        try {
            $userObject = $this->userRepo->getUser($userId);
            $friendsId = array();
            $userFriends = $userObject->getFriends()->where("status", 1)->get()->toArray();
            $friendsId = array_map(function ($friend) use ($friendsId) {
                $friendsId = $friend['friend_id'];
                return $friendsId;
            }, $userFriends);
            array_push($friendsId, $userId);
            $feeds = Feeds::whereIn('user_id', $friendsId)->orderBy('user_id', 'desc')->get();
            $response = array();
            foreach ($feeds as $feed) {
                $response[] = [
                    'user' => [
                        'name' => $feed->getUserInfo->first_name . " " . $feed->getUserInfo->last_name,
                        'image' => $feed->getUserInfo->profile_image_data,
                        'score' => $userObject->taskInformation->overall_score
                    ],
                    'feed' => [
                        'name' => $feed->getTaskInfo->task_name,
                        'title' => $feed->getTaskInfo->title,
                        'week' => $feed->week,
                        'badge' => $feed->badge,
                        'date' => is_null($feed->created_at) ? null : $feed->created_at->toDateTimeString()
                    ],
                ];
            }
            return Helpers::getResponse(200, "feeds", $response);
        } catch (UserNotFoundException $exception) {
            return $exception->sendUserNotFoundExceptionResponse();
        }
    }
}
