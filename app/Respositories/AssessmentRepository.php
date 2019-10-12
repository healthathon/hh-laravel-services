<?php

namespace App\Respositories;


use App\Model\Assess\queryTag;
use App\Model\AssessmentAnswers;

class AssessmentRepository
{

    public function __construct()
    {
    }

    /**
     * @return AssessmentAnswers[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllAssessAnswers()
    {
        return AssessmentAnswers::all();
    }
}