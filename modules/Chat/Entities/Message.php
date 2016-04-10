<?php namespace Modules\Chat\Entities;
   
use Illuminate\Database\Eloquent\Model;

class Message extends Model {

    protected $table = 'chatroom_messages';
    protected $fillable = ['chatroom_id', 'user_id', 'body'];

    public function permission()
    {
        return $this->belongsTo('Module\Chat\Entities\Permission', ['user_id', 'chatroom_id']);
    }

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

}