<?php

namespace App\Respositories;

use App\Model\SHAQuestionAnswers;

class SHAQuestionAnswersRepository extends BaseRepository
{

    public function __construct()
    {
        parent::__construct(new SHAQuestionAnswers());
    }

}