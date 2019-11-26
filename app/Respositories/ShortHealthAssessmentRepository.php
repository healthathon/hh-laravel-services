<?php

namespace App\Respositories;
use App\Exceptions\CategoryNotFoundException;
use App\Model\ShortHealthAssessment;


class ShortHealthAssessmentRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new ShortHealthAssessment());
    }
}