<?php

namespace App\Respositories;


use App\Constants;
use App\Exceptions\RegimenNotFoundException;
use App\Model\Assess\queryTag;
use App\Model\Tasks\taskBank;
use App\Model\Tasks\weeklyTask;


class TaskBankRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new taskBank());
    }

    public function getTaskByLevelAndCategory($categoryId, $level)
    {
        $tasks = $this->model->where('category', $categoryId)
            ->where('level', $level)
            ->orderBy('step')
            ->get()
            ->groupBy('task_name');
        return $tasks;
    }

    public function getTaskCountByLevelAndCategory($categoryId, $level)
    {
        return $this->model->where('category', $categoryId)
            ->where('level', $level)
            ->count();
    }

    public function getTaskBasedOnUserAssessmentAnswers($categoryId, $user)
    {
        $categoryRegimensId = $this->model->where('category', $categoryId)->pluck('id')->toArray();
        $recommendedTaskId = array_column($user->recommendedTask->toArray(), 'regimen_id');
        $recommendedTaskId = array_intersect($categoryRegimensId, $recommendedTaskId);
        $tasks = $this->model->whereIn('id', $recommendedTaskId)
            ->orderBy('step')
            ->get(['id', 'task_name', 'title', 'code'])
            ->groupBy('task_name');
        return $tasks;
    }

    public function getPopularTaskList($categoryId)
    {
        $tasks = $this->model->where('registered_users', '>', Constants::MIN_REGISTERED_USERS)
            ->where('category', $categoryId)
            ->orderBy('step')
            ->get(['id', 'task_name', 'title', 'code'])
            ->groupBy('task_name');
        return $tasks;
    }

    public function getTaskCategoryFromId($taskCode)
    {
        return $this->model->where('code', $taskCode)->first();
    }

    public function getCategoryRegimens(int $category)
    {
        return $this->model->where('category', $category)->get();
    }

    public function getNutritionRecommendedRegimen($userNutritionScore)
    {

    }

    /**
     * @param int $regimenId
     * @return mixed
     * @throws RegimenNotFoundException
     */
    function getRegimenById(int $regimenId)
    {
        $regimen = $this->model->where('id', $regimenId)->first();
        if ($regimen == null)
            throw new RegimenNotFoundException();
        return $regimen;
    }

    /**
     * @param string $regimenCode
     * @param array $options
     * @return mixed
     * @throws RegimenNotFoundException
     */
    public function getRegimenByCode(string $regimenCode, array $options = [])
    {
        $options = count($options) > 0 ? $options : null;
        $regimen = $this->model->where('code', $regimenCode)->first($options);
        if ($regimen == null)
            throw new RegimenNotFoundException();
        return $regimen;
    }

    public function getRegimenWeekDetails(string $regimenCode)
    {
        return (new WeeklyTaskRepository())->where('taskBank_id', $regimenCode)->orderBy('week')->get();
    }

    public function weekTaskObject(string $regimenCode, int $weekNo)
    {
        return (new WeeklyTaskRepository())->where('taskBank_id', $regimenCode)
            ->where('week', $weekNo)->first();
    }

    public function insertRegimen(array $regimenData)
    {
        return $this->model->create($regimenData);
    }

    public function deleteRegimen(string $regimenCode)
    {
        return $this->model->where('code', $regimenCode)->delete();
    }

    public function getNutritionRegimens()
    {
        $categoryId = (new QueryTagRepository())->where("tag_name", ucfirst("nutrition"))->first()->category_id;
        return $this->model->where("category", $categoryId)->get();
    }

    public function getTaskName($taskId)
    {
        $taskObject = $this->model->find($taskId);
        // Handle Null case later on
        return $taskObject != null ? $taskObject->task_name : "null";
    }

    public function getTaskBankObject($taskId)
    {
        return $this->model->where('id', $taskId)->first();
    }

    //data = Array
    public function updateTaskBank($id, $data)
    {
        $updateResponse = $this->model->where('id', $id)->update($data);
        return $updateResponse;
    }

    public function deleteTaskBank($id)
    {
        $deleteResponse = $this->model->where('id', $id)->delete();
        return $deleteResponse;
    }
}