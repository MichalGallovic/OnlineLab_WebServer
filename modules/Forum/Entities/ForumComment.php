<?php

namespace Modules\Forum\Entities;

use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    protected $table = 'forum_comments';

    public function user() {
        return $this->belongsTo('App\User','user_id');
    }

    public function thread() {
        return $this->belongsTo('Modules\Forum\Entities\ForumThread','thread_id');
    }
}
