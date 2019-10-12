<?php

namespace App\Services;


use App\Respositories\SHARepository;
use App\Services\Interfaces\ISHAService;
use Illuminate\Support\Arr;

class SHAServices implements ISHAService
{

    private $shaRepoObj;

    public function __construct(SHARepository $repository)
    {
        $this->shaRepoObj = $repository;
    }

    public function updateQuestionObj(array $itemArr, int $id)
    {
        $inputData = Arr::except($itemArr, "id");
        return $this->shaRepoObj->updateQuestion($id, $inputData);
    }

    function questions()
    {
        $questions = $this->shaRepoObj->fetchAllQuestions();
        $response = [];
        foreach ($questions as $question) {
            $response[] = [
                'id' => $question->id,
                'header' => $question->header,
                'multiple' => $question->multiple,
                'question' => $question->question,
                'is_scoreable' => $question->is_scoreable,
                'answers' => array_column($question->answers->toArray(), "answer"),
                'score' => array_column($question->answers->toArray(), "score")
            ];
        }
        return $response;
    }

    function insertQuestion(array $data)
    {
        $this->shaRepoObj->insertQuestion($data);
    }

    function deleteQuestion(int $id)
    {
        return $this->shaRepoObj->deleteQuestion($id);
    }
}