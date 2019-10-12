<?php

namespace App\Exceptions;

use App\Constants;
use App\Helpers;
use Exception;

class UserNotRegisteredForTask extends Exception
{

    protected $message;

    public function sendUserNotRegisteredForTaskExceptionResponse()
    {
        if ($this->message === null)
            $this->message = Constants::TASK_NOT_REGISTERED;
        return Helpers::getResponse(404, $this->message);
    }
}
