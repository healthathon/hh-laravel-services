<?php

namespace App\Services;


use App\Respositories\BMIRepository;
use App\Services\Interfaces\IBMIService;

class BMIService implements IBMIService
{

    private $bmiRepoObj;

    public function __construct(BMIRepository $bmiRepoObj)
    {
        $this->bmiRepoObj = $bmiRepoObj;
    }

    function bmiScores()
    {
        return $this->bmiRepoObj->fetchAllBMIScores();
    }

    function updateBMIScore(array $requestArr)
    {
        $id = $requestArr["id"];
        $score = $requestArr["score"];
        return $this->bmiRepoObj->updateBMIScore($id, $score);
    }
}