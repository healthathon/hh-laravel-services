<?php

/**
 * This Class represents ForgotPasswordMail Listeners
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  App\Listeners
 * @version  v.1.1
 */

namespace App\Listeners;

use App\Events\SendForgotPasswordMail;
use App\Http\Controllers\MailController;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class ForgotPasswordMailListeners
 *
 * This is an listeners which keeps on Listening Events#SendForgotPasswordMail and on
 * firing an event this class executes an handle function which sends an email.
 */
class ForgotPasswordMailListeners
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event which sends an email by invoking
     * @see MailController#sendForgotPasswordEmail function
     *
     * @param  SendForgotPasswordMail $event
     * @return void
     */
    public function handle(SendForgotPasswordMail $event)
    {
        $user = $event->user;
        $password = $event->newPassword;
        MailController::sendForgotPasswordEmail($user, $password);
    }
}
