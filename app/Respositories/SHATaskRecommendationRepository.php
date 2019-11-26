<?php

namespace App\Respositories;

use App\Model\BmiScore;
use App\Model\SHATaskRecommendation;
use Illuminate\Database\Eloquent\Model;

class SHATaskRecommendationRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new SHATaskRecommendation());
    }
}