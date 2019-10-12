<?php

namespace App\Services;

/**
 * Class ModuleScoreServices : Calculate Score Of User in Each Module
 * @author  Mayank Jariwala
 * @package App\Services
 */
class ModuleScoreServices
{

    private $multiplier;

    public function __construct()
    {
        $this->baseXPLevel = -1;
        $this->multiplier = -1;
    }

    public function getUserCurrentModuleScore($taskLevel, $noOfTaskCompletedByUserInThisModule)
    {
        return $this->calculateScoreForCurrentAssessModule($taskLevel, $noOfTaskCompletedByUserInThisModule);
    }

    private function calculateScoreForCurrentAssessModule($taskLevel, $noOfTaskCompletedByUserInThisModule)
    {
        // Formula 3 Sections
        $section1 = $this->valueOfA() * $taskLevel + $taskLevel;
        $baseXPLevel = $this->baseXPAndMultiplierForAssessModule($taskLevel);
        $section2 = $baseXPLevel;
        $section3 = $noOfTaskCompletedByUserInThisModule ** 1.4;
        $score = $section1 * $section2 * $section3;
        return ceil($score);
    }

    /**
     *  a  : Multiplier for difficulty level
     * Currently it is static but it may varies in future so function is created
     * @author  Mayank Jariwala
     */
    private function valueOfA()
    {
        return 3;
    }

    /**
     *  LifeStyle/Nutrition/Physical/Mental
     * @author  Mayank Jariwala
     * @param $taskLevel : Task Level
     * @return null
     */
    private function baseXPAndMultiplierForAssessModule($taskLevel)
    {
        switch ($taskLevel) {
            case 1:
                $baseXPLevel = 10;
                $this->multiplier = 5;
                break;
            case 2:
                $baseXPLevel = 20;
                $this->multiplier = 6;
                break;
            case 3:
                $baseXPLevel = 30;
                $this->multiplier = 7;
                break;
            case 4:
                $baseXPLevel = 40;
                $this->multiplier = 8;
                break;
            default:
                $baseXPLevel = -1;
                $this->multiplier = -1;
                break;
        }
        return $baseXPLevel;
    }
}