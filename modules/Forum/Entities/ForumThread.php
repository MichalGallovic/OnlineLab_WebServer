<?php

namespace Modules\Forum\Entities;

use Illuminate\Database\Eloquent\Model;

class ForumThread extends Model
{
    protected $table = 'forum_threads';

    public function user() {
        return $this->belongsTo('App\User','user_id');
    }

    public function category() {
        return $this->belongsTo('Modules\Forum\Entities\ForumCategory','category_id');
    }

    public function comments() {
        return $this->hasMany('Modules\Forum\Entities\ForumComment', 'thread_id');
    }
}