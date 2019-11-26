<?php

namespace App\Respositories;

use App\Model\TaskCorrespondingAssessmentAnswers;

class TaskCorrespondingAssessmentAnswersRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new TaskCorrespondingAssessmentAnswers());
    }

}