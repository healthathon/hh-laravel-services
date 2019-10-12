<?php

namespace App\Services\Interfaces;


interface IBMIService
{

    function bmiScores();

    function updateBMIScore(array $inputArr);
}