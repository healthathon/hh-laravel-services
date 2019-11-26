<?php

namespace App\Exceptions;

use App\Constants;
use App\Helpers;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class UserNotFoundException
 *
 * This is an exception class, which is thrown when application try to get an user object
 * from userId, but it is not able to find in database at that time this exception is raised
 *
 * @package App\Exceptions
 */
class UserNotFoundException extends Exception
{
    protected $message;

    /**
     * @param $message
     */
    public function setMessage($message): void
    {
        dd("asdas");
        $this->message = $message;
    }

    public function report($message)
    {
        Log::error("User not found " . $message);
    }

    public function sendUserNotFoundExceptionResponse()
    {
        if ($this->message === null)
            $this->message = Constants::NO_USER_FOUND;
        return Helpers::getResponse(404, $this->message);
    }
}
