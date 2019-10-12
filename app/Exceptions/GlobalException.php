<?php

namespace App\Exceptions;

use App\Helpers;
use Exception;
use Throwable;

class GlobalException extends Exception
{

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function sendGlobalExceptionResponse($e)
    {
        return Helpers::getResponse(404, $e->getMessage());
    }

    public function sendNotEligibleException()
    {
        return Helpers::getResponse(404, "User already completed task or delay");
    }
}
