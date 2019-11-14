<?php

namespace App\Listeners;

use App\Events\SendMMGBookingMailToAdmin;
use App\Http\Controllers\MailController;
use App\Model\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMMGBookingMailToAdminListener
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
    public function handle(SendMMGBookingMailToAdmin $event)
    {
        MailController::sendMMGBookingMailToAdmin($event->user,$event->data);
    }
}
