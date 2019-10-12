<?php

namespace App\Exceptions;
use App\Helpers;
use Exception;
use Throwable;

class ThyrocareResponseException extends Exception
{

    private $exceptionMessage;

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $this->exceptionMessage = $message;
        parent::__construct($message, $code, $previous);
    }

    public function sendThyrocareExceptionResponse()
    {
        return Helpers::getResponse(400, $this->exceptionMessage);
    }
}