<?php

namespace App\Respositories;

use App\Model\Assess\AssessmentQuestionsTagOrder;

class AssessmentQuestionsTagOrderRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new AssessmentQuestionsTagOrder());
    }

    public function getRequestedIdOrderSequence($id)
    {
        $assessmentQuestionOrder = $this->model->where('id', $id)->first();
        return $assessmentQuestionOrder == null ? $this->getActiveOrderSequence() : $assessmentQuestionOrder;
    }

    public function getActiveOrderSequence()
    {
        return $this->model->where('is_active', 1)->first();
    }
}