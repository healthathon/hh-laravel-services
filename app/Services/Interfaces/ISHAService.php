<?php

namespace App\Services\Interfaces;


interface ISHAService
{
    function questions();

    function insertQuestion(array $data);

    function updateQuestionObj(array $data, int $id);

    function deleteQuestion(int $id);
}