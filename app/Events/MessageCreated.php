<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
     /**
     *
     * @var \App\Models\Message
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Message $message)
    {
       $this->message = $message ;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // event peer to peer chat
        $other_user = $this->message->conversation->participants()
        ->where('user_id' , '<>', $this->message->user_id)
        ->first();
        return new PresenceChannel('messenger.'.$other_user->id);
    }

    public function broadcastAs(){
        return 'new-message';
    }
}
