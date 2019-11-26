<?php
/**
 * Created by PhpStorm.
 * User: MayankJariwala
 * Date: 24-Apr-19
 * Time: 6:59 PM
 */

namespace App\Services;


use App\Model\Feeds;
use App\Respositories\FeedRepository;
use App\Respositories\WeeklyTaskRepository;

class FeedsService
{
    private $weekTaskRepo;

    public function __construct()
    {
        $this->weekTaskRepo = new WeeklyTaskRepository();
    }

    public function addUsersFeeds($user_id, $day, $week_number, $taskBankId)
    {
        $weekTaskObj = $this->weekTaskRepo->getWeekTaskObject($taskBankId, $week_number);
        $feeds = null;
        $dayImageColumn = "day$day" . "_badge";
        $feeds = (new FeedRepository())->updateOrCreate([
            'user_id' => $user_id, 'week' => $week_number, 'day' => $day, 'task' => $taskBankId
        ], [
            'badge' => $weekTaskObj->$dayImageColumn
        ]);
        return $feeds;
    }
}