<?php

namespace App\Respositories;
use App\Model\BmiScore;

class BmiScoreRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new BmiScore());
    }
}