<?php

namespace App\Respositories;


use App\Model\Tasks\weeklyTask;

class WeeklyTaskRepository
{
    private $taskBankRepo;

    public function __construct()
    {
        $this->taskBankRepo = new TaskBankRepository();
    }

    function findWeekTaskByWeekNoAndCode(int $weekNo, string $regimenCode, array $options = [])
    {
        $options = count($options) > 0 ? $options : null;
        return weeklyTask::where('week', $weekNo)->where('taskBank_id', $regimenCode)->first($options);
    }

    function updateWeekObject(string $regimenCode, int $week, array $dataToUpdate)
    {
        return weeklyTask::where('week', $week)->where('taskBank_id', $regimenCode)->update($dataToUpdate);
    }

    public function insertWeekTask(array $weekDetails)
    {
        return weeklyTask::updateOrCreate([
            'week' => $weekDetails["week"],
            'taskBank_id' => $weekDetails["taskBank_id"]
        ], $weekDetails);
    }

    public function deleteWeeklyTask(int $week, string $code)
    {
        return weeklyTask::where('week', $week)
            ->where('taskBank_id', $code)
            ->delete();
    }

    /**
     * @param int $taskBankId
     * @param int $weekNo
     * @return mixed
     * @throws \App\Exceptions\RegimenNotFoundException
     */
    public function getWeekTaskObject(int $taskBankId, int $weekNo)
    {
        $regimenObject = $this->taskBankRepo->getRegimenById($taskBankId);
        return weeklyTask::where('taskBank_id', $regimenObject->code)
            ->where('week', $weekNo)
            ->first();
    }
}