<?php

namespace App\Respositories;


use App\Constants;
use App\Exceptions\RegimenNotFoundException;
use App\Model\Assess\queryTag;
use App\Model\Tasks\taskBank;
use App\Model\Tasks\weeklyTask;

class TaskBankRepository
{
    public function getTaskByLevelAndCategory($categoryId, $level)
    {
        $tasks = taskBank::where('category', $categoryId)
            ->where('level', $level)
            ->orderBy('step')
            ->get()
            ->groupBy('task_name');
        return $tasks;
    }

    public function getTaskCountByLevelAndCategory($categoryId, $level)
    {
        return taskBank::where('category', $categoryId)
            ->where('level', $level)
            ->count();
    }

    public function getTaskBasedOnUserAssessmentAnswers($categoryId, $user)
    {
        $categoryRegimensId = taskBank::where('category', $categoryId)->pluck('id')->toArray();
        $recommendedTaskId = array_column($user->recommendedTask->toArray(), 'regimen_id');
        $recommendedTaskId = array_intersect($categoryRegimensId, $recommendedTaskId);
        $tasks = taskBank::whereIn('id', $recommendedTaskId)
            ->orderBy('step')
            ->get(['id', 'task_name', 'title', 'code'])
            ->groupBy('task_name');
        return $tasks;
    }

    public function getPopularTaskList($categoryId)
    {
        $tasks = taskBank::where('registered_users', '>', Constants::MIN_REGISTERED_USERS)
            ->where('category', $categoryId)
            ->orderBy('step')
            ->get(['id', 'task_name', 'title', 'code'])
            ->groupBy('task_name');
        return $tasks;
    }

    public function getTaskCategoryFromId($taskCode)
    {
        return taskBank::where('code', $taskCode)->first();
    }

    public function getCategoryRegimens(int $category)
    {
        return taskBank::where('category', $category)->get();
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
        $regimen = taskBank::where('id', $regimenId)->first();
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
        $regimen = taskBank::where('code', $regimenCode)->first($options);
        if ($regimen == null)
            throw new RegimenNotFoundException();
        return $regimen;
    }

    public function getRegimenWeekDetails(string $regimenCode)
    {
        return weeklyTask::where('taskBank_id', $regimenCode)->orderBy('week')->get();
    }

    public function weekTaskObject(string $regimenCode, int $weekNo)
    {
        return weeklyTask::where('taskBank_id', $regimenCode)
            ->where('week', $weekNo)->first();
    }

    public function insertRegimen(array $regimenData)
    {
        return taskBank::create($regimenData);
    }

    public function deleteRegimen(string $regimenCode)
    {
        return taskBank::where('code', $regimenCode)->delete();
    }

    public function getNutritionRegimens()
    {
        $categoryId = queryTag::where("tag_name", ucfirst("nutrition"))->first()->category_id;
        return taskBank::where("category", $categoryId)->get();
    }
}