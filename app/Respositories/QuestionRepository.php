<?php

namespace App\Respositories;

class QuestionRepository
{

    function getQuestionById(int $questionId,array $options = [])
    {
        $options = count($options) > 0 ? $options : null;
        return (new QueryRepository())->where('id', $questionId)->first($options);
    }
}