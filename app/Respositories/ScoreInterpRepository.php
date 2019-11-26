<?php

namespace App\Respositories;


use App\Model\Assess\scoreInterp;

class ScoreInterpRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new scoreInterp());
    }
}