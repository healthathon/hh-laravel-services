<?php

namespace App\Exceptions;

use App\Constants;
use App\Helpers;
use Exception;

class NoRecommendationException extends Exception
{

    protected $message;

    public function noRecommendationAdviseResponse()
    {
        if ($this->message === null)
            $this->message = Constants::NO_RECOMMENDATION_ADVISE_MESSAGE;
        return Helpers::getResponse(401, $this->message);
    }
}
