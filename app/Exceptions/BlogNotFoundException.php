<?php

namespace App\Exceptions;

use App\Constants;
use App\Helpers;
use Exception;
use Illuminate\Support\Facades\Log;

class BlogNotFoundException extends Exception
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
        Log::error("Blog not found " . $message);
    }

    public function sendBlogNotFoundException()
    {
        if ($this->message === null)
            $this->message = Constants::NO_BLOG_FOUND;
        return Helpers::getResponse(404, $this->message);
    }
}
