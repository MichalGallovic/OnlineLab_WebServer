<?php namespace Modules\Chat\Entities;
   
use Illuminate\Database\Eloquent\Model;

class Chatroom extends Model {


    protected $table = "chatrooms";

    protected $fillable = ['title', 'type'];

    public function users()
    {
        return $this->belongsToMany('App\User', 'chatroom_permissions');
    }

    public function messages(){
        return $this->hasMany('Modules\Chat\Entities\Message', 'chatroom_id');
    }

    public function permissions(){
        return $this->hasMany('Modules\Chat\Entities\Permission', 'chatroom_id');
    }

    public function getCreator(){
        return $this->permissions()->where('type', 'creator')->first()->user->name;
    }

    public function canPost($user_id){
        if($this->type == 'public_open' || $this->type == 'private'){
            return true;
        }else if(sizeof(Permission::where(['chatroom_id'=>$this->id, 'user_id'=>$user_id])->whereIn('type', ['admin', 'creator', 'member'])->get())>0){
            return true;
        }
        return false;
    }
}