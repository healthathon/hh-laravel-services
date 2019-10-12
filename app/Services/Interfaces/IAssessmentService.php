<?php


namespace App\Services\Interfaces;


interface IAssessmentService
{
    function fetchAllAssessAnswers();

    function updateQueryAnswersRegimen(string $answer,int $queryId,array $regimenIds);
}