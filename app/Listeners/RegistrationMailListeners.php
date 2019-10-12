<?php

/**
 * This Class represents RegistrationMail Listeners
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  App\Listeners
 * @version  v.1.1
 */

namespace App\Listeners;

use App\Events\SendRegistrationMail;
use App\Http\Controllers\MailController;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class ForgotPasswordMailListeners
 *
 * This is an listeners which keeps on Listening Events#sendRegistrationMail and on
 * firing an event this class executes an handle function which sends an email.
 */
class RegistrationMailListeners
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
     * Handle the event function send an registration mail by invoking
     * @see MailController#sendRegistrationMail method.
     *
     * @param  SendRegistrationMail $event
     * @return void
     */
    public function handle(SendRegistrationMail $event)
    {
        $user = $event->user;
        MailController::sendRegistrationMail($user);
    }
}
