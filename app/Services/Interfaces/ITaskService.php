<?php

namespace App\Services\Interfaces;


use App\Model\User;

interface ITaskService
{
    function getRecommendedTask(int $userId, string $category);

    function getPopularTask(User $user, string $category);

    function dailyTaskDone(int $taskId, int $userId, bool $isMixedBag);

    function registerTask(int $userId, int $taskId, bool $isMixedBag);

    function unregisterTask(int $userId, int $taskId, bool $isMixedBag);

    function getUserTasksCount(User $userId);

    function getCategoryRegimens(int $category);

    function uploadDailyBadge(string $regimenCode, int $week, int $day, string $filePath);

    function uploadRegimenBadge(int $regimenId, string $fileType, string $fileContent);

    function regimenByCode(string $regimenCode, array $options = []);

    function regimenWeekDetails(string $regimenCode);

    function weekTaskObject(string $regimenCode, int $weekNo);

    function updateRegimenWeek(string $regimenCode, int $week, array $dataToUpdate);

    function createNewRegimen(array $data);

    function addWeekTask(array $weekTaskData);

    function deleteWeekTask(int $week, string $code);

    function deleteRegimen(string $regimenCode);
}