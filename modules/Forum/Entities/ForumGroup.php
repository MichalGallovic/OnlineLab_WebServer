<?php namespace Modules\Forum\Entities;

use Illuminate\Database\Eloquent\Model;

class ForumGroup extends Model
{
    protected $table = 'forum_groups';

    public function categories() {
        return $this->hasMany('Modules\Forum\Entities\ForumCategory', 'group_id');
    }
}