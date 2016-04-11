<?php namespace Modules\Chat\Entities;
   
use Illuminate\Database\Eloquent\Model;

class Permission extends Model {

    protected $table = 'chatroom_permissions';
    protected $fillable = ['chatroom_id', 'user_id', 'type'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function chatroom()
    {
        return $this->belongsTo('Modules\Chat\Entities\Chatroom', 'chatroom_id');
    }

    public function messages()
    {
        return $this->hasMany('Module\Chat\Entities\Message', ['user_id', 'chatroom_id']);
    }
}