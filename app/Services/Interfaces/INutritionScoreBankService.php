<?php

namespace App\Services\Interfaces;


interface INutritionScoreBankService
{

    function insertScoreBank(array $request);

    function getAllScoreBankData();

    function getScoreBankById(int $id);

    function updateScoreBank(int $id, array $request);

    function getRecommendedRegimenIds(int $tagScore);
}