<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MemberAdded extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $username, $addressId, $body, $chatroomName, $chatroomId, $video;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($addedUserId, $addedUserName, $addedByName, $chatroomName, $chatroomId, $video = false)
    {
        $this->username = $addedUserName;
        $this->addressId = $addedUserId;
        $this->chatroomName = $chatroomName;
        $this->chatroomId = $chatroomId;
        $this->video = $video;
        $this->body = $addedByName.($video ?  trans("controller::default.CHAT_VIDEO_NOTIFICATION") : trans("controller::default.CHAT_NOTIFICATION")).' <a href="'.url(($video ? "chat/video" : "chat"), [$chatroomId]).'">' .$chatroomName. '</a> ';
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
