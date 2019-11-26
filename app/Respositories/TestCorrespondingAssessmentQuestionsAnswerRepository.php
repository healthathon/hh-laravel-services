<?php

namespace App\Respositories;

use App\Model\TestCorrespondingAssessmentQuestionsAnswer;

class TestCorrespondingAssessmentQuestionsAnswerRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new TestCorrespondingAssessmentQuestionsAnswer());
    }

}