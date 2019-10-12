<?php

namespace App\Exceptions;

use App\Helpers;
use Exception;

class NoAssessmentFoundException extends Exception
{
    protected $message;

    /**
     * @param $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    public function report($message)
    {
        Log::error("User not found " . $message);
    }

    public function sendNoAssessmentFoundResponse()
    {
        return Helpers::getResponse(404, $this->message);
    }
}
