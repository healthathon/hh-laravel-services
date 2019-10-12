<?php

namespace App;


class Constants
{
    const SOCIAL_LOGIN = "social";
    const NO_USER_FOUND = "No user found";
    const NO_ASSESSMENT_FOUND = "Assessment not completed";
    const ASSESSMENT_NOT_YET_STARTED = "Assessment not started yet";
    const PHYSICAL = "physics";
    const BMI = "bmi";
    const EXCELLENT = "Excellent";
    const GOOD = "Good";
    const BAD = "Bad";
    const PHYSICS = "Physics";
    const NUTRITION = "Nutrition";
    const MENTAL = "Mental";
    const LIFESTYLE = "Lifestyle";
    const TASK_SUCCESS_REGISTER = "Congratulations you are registered for this regimen";
    const TASK_ALREADY_REGISTER = "You already registered for this regimen";
    const TASK_SUCCESS_UNREGISTER = "You are removed from requested regimen";
    const TASK_NOT_REGISTERED = "You are not registered for this regimen";
    const RESET_ASSESSMENT = "Data Reset";
    const RESETTING_FAILED = "Resetting Failed";
    const AMAZON_FILE_WRITING_FAILED = "File Write Issues";
    const MIN_REGISTERED_USERS = 30;
    const NO_BLOG_FOUND = "Blog not found";
    const NO_REGIMEN_FOUND = "No regimen found";

    const ADD = "add";
    const REMOVE = "remove";
    const UPDATE = "update";

    const THYROCARE = "thyrocare";
    const MAPMYGENOME = "mapmygenome";
    const NO_RECOMMENDATION_ADVISE_MESSAGE = "We are unable to recommend a regimen at the moment.Please seek your doctor's advice before starting any new regimen";
    const CATEGORY_NOT_FOUND = "No category found";

    const EMOTIONAL_WELL_BEING = "Emotional Well Being";
    const AGE_RESTRICTION = "Sorry, we can only recommend tasks for ages between 18-60 years";

    static function taskLimitExceeded($categoryName)
    {
        $categoryName = strtolower($categoryName) === "physics" ? "Physical" : ucfirst($categoryName);
        return "You have already registered for 2 regimen in $categoryName category";
    }
}