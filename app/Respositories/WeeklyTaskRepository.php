<?php

namespace App\Respositories;


use App\Model\Tasks\weeklyTask;

class WeeklyTaskRepository extends BaseRepository
{
    private $taskBankRepo;

    public function __construct()
    {
        parent::__construct(new weeklyTask());
        $this->taskBankRepo = new TaskBankRepository();
    }

    function findWeekTaskByWeekNoAndCode(int $weekNo, string $regimenCode, array $options = [])
    {
        $options = count($options) > 0 ? $options : null;
        return $this->model->where('week', $weekNo)->where('taskBank_id', $regimenCode)->first($options);
    }

    function updateWeekObject(string $regimenCode, int $week, array $dataToUpdate)
    {
        return $this->model->where('week', $week)->where('taskBank_id', $regimenCode)->update($dataToUpdate);
    }

    public function insertWeekTask(array $weekDetails)
    {
        return $this->model->updateOrCreate([
            'week' => $weekDetails["week"],
            'taskBank_id' => $weekDetails["taskBank_id"]
        ], $weekDetails);
    }

    public function deleteWeeklyTask(int $week, string $code)
    {
        return $this->model->where('week', $week)
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
        return $this->model->where('taskBank_id', $regimenObject->code)
            ->where('week', $weekNo)
            ->first();
    }

    public function getTaskTotalWeeks($task_id)
    {
        return $this->model->where('taskBank_id', $task_id)->count();
    }

    public function getDayCompleteMessage($day, $weekNo, $taskBankId)
    {
        $column = "day" . $day . "_message";
        $data = $this->model->where('week', $weekNo)
            ->where('taskBank_id', $taskBankId)
            ->first([$column]);
        return $data->$column;
    }
}