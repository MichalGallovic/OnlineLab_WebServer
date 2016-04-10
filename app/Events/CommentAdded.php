<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CommentAdded extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $username, $threadTitle, $addressId, $body;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($username, $threadTitle, $threadId)
    {
        $this->username = $username;
        $this->addressId = $threadId;
        $this->threadTitle = $threadTitle;
        $this->body = $username . ' commented on thread <a href="' .url('forum/thread', [$threadId]).' ">'.$threadTitle.'</a>';
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['notification-channel'];
    }
}
