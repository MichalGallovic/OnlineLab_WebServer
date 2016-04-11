<?php

namespace Modules\Forum\Entities;
use Illuminate\Database\Eloquent\Model;

class ForumCategory extends Model
{
    protected $table = 'forum_categories';

    public function gruop() {
        return $this->belongsTo('Modules\Forum\Entities\ForumGroup','group_id');
    }

    public function user() {
        return $this->belongsTo('App\User','user_id');
    }

    public function threads() {
        return $this->hasMany('Modules\Forum\Entities\ForumThread', 'category_id');
    }
}
