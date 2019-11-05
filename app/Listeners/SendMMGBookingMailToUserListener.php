<?php

namespace App\Listeners;

use App\Events\SendMMGBookingMailToUser;
use App\Http\Controllers\MailController;
use App\Model\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMMGBookingMailToUserListener
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
     * @param SendMMGBookingMailToUser $event
     * @return void
     */
    public function handle(SendMMGBookingMailToUser $event)
    {
        MailController::sendMMGBookingMailToUser($event->user);
    }
}
