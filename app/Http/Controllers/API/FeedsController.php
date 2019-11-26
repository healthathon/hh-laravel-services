<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\UserNotFoundException;
use App\Helpers;
use App\Model\Feeds;
use App\Respositories\FeedRepository;
use App\Respositories\UserRepository;
use App\Respositories\UsersTaskInformationRepository;
use App\Services\FeedsService;
use App\Http\Controllers\Controller;

class FeedsController extends Controller
{

    private $feedsService, $userRepo,$feedRepo;

    public function __construct()
    {
        $this->feedsService = new FeedsService();
        $this->userRepo = new UserRepository();
        $this->feedRepo = new FeedRepository();
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
//            dd($userFriends);

            $friendsId = array_map(function ($friend) use ($friendsId) {
                $friendsId = $friend['friend_id'];
                return $friendsId;
            }, $userFriends);

            array_push($friendsId, $userId);

            $fridnIdString = implode(",",$friendsId);
//            dd($fridnIdString);
            $UsersTaskInformations = (new UsersTaskInformationRepository())->selectRaw("id,user_id,FIND_IN_SET( overall_score, ( SELECT GROUP_CONCAT( overall_score ORDER BY overall_score DESC ) FROM users_task_informations WHERE user_id IN(".$fridnIdString.")) ) AS rank")->whereIn("user_id",array_merge([$userId],$friendsId))->get();

            $userRank = [];
            foreach ($UsersTaskInformations as $uInfo){
                $userRank[$uInfo->user_id] = $uInfo->rank;
            }

//            dd($UsersTaskInformations->toArray());

            $feeds = $this->feedRepo->whereIn('user_id', $friendsId)->orderBy('user_id', 'desc')->get();
            $response = array();
            foreach ($feeds as $feed) {
                $response[] = [
                    'user' => [
                        'id' => $feed->getUserInfo->id,
                        'name' => $feed->getUserInfo->first_name . " " . $feed->getUserInfo->last_name,
                        'image' => $feed->getUserInfo->profile_image_data,
                        'score' => $userObject->taskInformation->overall_score,
                        'rank' => isset($userRank[$feed->getUserInfo->id]) ? $userRank[$feed->getUserInfo->id]:0
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
