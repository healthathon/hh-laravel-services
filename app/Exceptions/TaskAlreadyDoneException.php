<?php


namespace App\Exceptions;
use Exception;


use App\Helpers;

class TaskAlreadyDoneException extends Exception
{

    public function sendTaskAlreadyDoneException()
    {
        return Helpers::getResponse(400, "You already completed today's task");
    }
}