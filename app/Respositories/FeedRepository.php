<?php

namespace App\Respositories;
use App\Model\Feeds;

class FeedRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new Feeds());
    }

    /**
     * Avoid adding duplicate entry into feeds table
     *
     * @param $user_id : User Identification Number
     * @param $week_number : Week Number of Task Completion
     * @param $task_number :  Id of Task Completed by User
     * @return bool: Returns whether such entry exists
     */
    public function checkEntryExists($user_id, $week_number, $task_number)
    {
        $recordCount = $this->model->where('user_id', $user_id)->where('week', $week_number)->where('task', $task_number)->count();
        if ($recordCount != 0)
            return true;
        else
            return false;
    }
}