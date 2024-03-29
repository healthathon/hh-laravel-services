<?php

/**
 * This Class represents SendRegistrationMail Event which fires on external call
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  App\Events
 * @version  v.1.1
 */

namespace App\Events;

use App\Model\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class SendRegistrationMail
 *
 * This event is fire when new user register into an application
 */
class SendRegistrationMail
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    /**
     * Create a new event instance.
     *
     * @param User $user : User Object
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
