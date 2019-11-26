<?php
namespace App\Respositories;
use App\Model\MixedBagUserHistory;


class MixedBagUserHistoryRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new MixedBagUserHistory());
    }

    /**
     * This function fetch user object who is doing MixedBag Task
     *
     * @param $userId : User ID
     * @param $regimenId : MixedBag Regimen ID
     * @return $mixed|null:  Return Object of user or null
     */
    public function getUserMbObject($userId, $regimenId)
    {
        if (!$this->userDoingRegimen($userId, $regimenId))
            return null;
        else
            return $this->model->where('user_id', $userId)->where('regimen_id', $regimenId)->first();
    }

    /**
     * This function checks whether user is doing an regimen
     *
     * @param $userId : User ID
     * @param $regimenId : MixedBag Regimen ID
     * @return mixed
     */
    public function userDoingRegimen($userId, $regimenId)
    {
        return $this->model->where('user_id', $userId)->where('regimen_id', $regimenId)->exists();
    }

    /**
     * This function fetch user doing mixed bag tasks
     *
     * @param $userId : User ID
     * @param $regimenId : MixedBag Regimen ID
     * @return mixed
     */
    public function getUserDoingTasks($userId, $regimenId)
    {
        return $this->model->where('user_id', $userId)
            ->where('category', $regimenId)
            ->pluck('regimen_id')
            ->toArray();
    }

    /**
     * This function removes/unregister user from mixed bag task
     *
     * @param $userId : User ID
     * @param $regimenId : MixedBag Regimen ID
     * @return mixed
     */
    public function removeUser($userId, $regimenId)
    {
        return $this->model->where('user_id', $userId)
            ->where('regimen_id', $regimenId)
            ->delete();
    }
}