<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MemberAdded extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $username, $addressId, $body;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($addedUserId, $addedUserName, $addedByName, $chatroomName, $chatroomId)
    {
        $this->username = $addedUserName;
        $this->addressId = $addedUserId;
        $this->body = $addedByName. ' invited you to chatroom: <a href="' .url('chat', [$chatroomId]).'">' .$chatroomName. '</a> ';
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
