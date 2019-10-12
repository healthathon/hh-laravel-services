<?php

namespace App\Exceptions;

use App\Constants;
use App\Helpers;
use Exception;

class RestrictionLevelException extends Exception
{

    public function sendRestrictionLevelException()
    {
        return Helpers::getResponse(400, Constants::NO_RECOMMENDATION_ADVISE_MESSAGE);
    }

}
