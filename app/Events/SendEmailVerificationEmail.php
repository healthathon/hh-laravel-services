<?php

/**
 * This Class represents SendEmailVerificationEmail Event which fires on external call
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  App\Events
 * @version  v.1.1
 */

namespace App\Events;

use App\Model\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Class SendEmailVerificationEmail
 *
 * This event is fire when new user register into an application and system wants to verify user
 * email in order to activate account
 */
class SendEmailVerificationEmail
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var User User Object
     */
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
