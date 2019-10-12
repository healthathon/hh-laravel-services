<?php

namespace App\Respositories;


use App\Model\Assess\Query;

class QuestionRepository
{

    function getQuestionById(int $questionId,array $options = [])
    {
        $options = count($options) > 0 ? $options : null;
        return Query::where('id', $questionId)->first($options);
    }
}