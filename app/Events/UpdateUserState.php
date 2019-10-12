<?php

namespace App\Events;

use App\Model\Assess\assesHistory;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UpdateUserState
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userAssesObject;

    /**
     * Create a new event instance.
     *
     * @param assesHistory $userAssesObject
     */
    public function __construct(assesHistory $userAssesObject)
    {
        $this->userAssesObject = $userAssesObject;
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
