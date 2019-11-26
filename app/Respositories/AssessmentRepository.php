<?php

namespace App\Respositories;


use App\Model\Assess\queryTag;
use App\Model\AssessmentAnswers;

class AssessmentRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new AssessmentAnswers());
    }

    /**
     * @return AssessmentAnswers[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllAssessAnswers()
    {
        return $this->model->all();
    }
}