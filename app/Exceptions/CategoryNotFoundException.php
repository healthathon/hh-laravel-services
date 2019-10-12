<?php

namespace App\Exceptions;

use App\Constants;
use App\Helpers;
use Exception;

class CategoryNotFoundException extends Exception
{

    public function sendCategoryNotFoundExceptionResponse()
    {
        return Helpers::getResponse(404, Constants::CATEGORY_NOT_FOUND);
    }
}
