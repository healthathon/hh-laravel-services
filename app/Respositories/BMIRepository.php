<?php

namespace App\Respositories;


use App\Model\BmiScore;

class BMIRepository
{

    public function fetchAllBMIScores()
    {
        return BmiScore::all();
    }

    public function updateBMIScore(int $id, int $score)
    {
        return BmiScore::where('id', $id)->update(['score' => $score]);
    }
}