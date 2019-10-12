<?php

namespace App\Services;


use App\Respositories\QuestionRepository;

class QuestionService
{
    private $questionRepo;

    public function __construct()
    {
        $this->questionRepo = new QuestionRepository();
    }

    function fetchQuestionById(int $questionId, array $options = [])
    {
        return $this->questionRepo->getQuestionById($questionId, $options);
    }
}