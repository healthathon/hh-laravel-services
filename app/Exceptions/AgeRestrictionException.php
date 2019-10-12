<?php

namespace App\Exceptions;

use App\Constants;
use App\Helpers;
use Exception;

class AgeRestrictionException extends Exception
{

    protected $message;

    public function ageRestrictionMessageResponse()
    {
        if ($this->message === null)
            $this->message = Constants::AGE_RESTRICTION;
        return Helpers::getResponse(401, $this->message);
    }
}
