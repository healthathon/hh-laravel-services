<?php

namespace App\Respositories;

use App\Model\BmiScore;
use Illuminate\Database\Eloquent\Model;

class BMIRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new BmiScore());
    }

    public function fetchAllBMIScores()
    {
        return $this->model->all();
    }

    public function updateBMIScore(int $id, int $score)
    {
        return $this->model->where('id', $id)->update(['score' => $score]);
    }
}