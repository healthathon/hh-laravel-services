<?php

namespace App\Respositories;
use App\Model\UserTask;

class UserTaskRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new UserTask());
    }

    /**
     * This function returns all task user is doing - This relationship should be in user model
     * TODO: re-shifting require [Mayank Jariwala]
     * @param $user_id : USER ID
     * @return \stdClass
     */
    public function getUserTask($user_id)
    {
        $user_task = new \stdClass();
        $temps = $this->model->where('user_id', $user_id)->first();
        if (!is_null($temps))
            return $temps;
        return $user_task;
    }
}