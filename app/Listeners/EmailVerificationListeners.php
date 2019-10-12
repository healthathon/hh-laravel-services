<?php

/**
 * This Class represents EmailVerification Listeners
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  App\Listeners
 * @version  v.1.1
 */

namespace App\Listeners;

use App\Events\SendEmailVerificationEmail;
use App\Http\Controllers\MailController;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class EmailVerificationListeners
 *
 * This is an listeners which keeps on Listening Events#sendEmailVerificationEmail and on
 * firing an event this class executes an handle function which sends an email.
 */
class EmailVerificationListeners
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
     * Handle the event.
     *
     * @param  SendEmailVerificationEmail $event
     * @return void
     */
    public function handle(SendEmailVerificationEmail $event)
    {
        MailController::sendEmailVerificationEmail();
    }
}
