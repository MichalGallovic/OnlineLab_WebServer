<?php

namespace App;

use Modules\Chat\Entities\Chatroom;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Reservation\Entities\Reservation;
use Illuminate\Auth\Passwords\CanResetPassword;
use Modules\Experiments\Entities\PhysicalDevice;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;


class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'surname', 'avatar', 'language_code', 'role'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    //protected $hidden = ['remember_token'];

    public function isAdmin(){
        return  $this->role=='admin';
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    public function hasAccount($accountType){
        foreach($this->accounts as $account){
            if($account->type == $accountType){
                return true;
            }
        }
        return false;
    }

    public function getFullName(){
        return $this->name.' '.$this->surname;
    }

    public function getAccount($accountType){
        foreach($this->accounts as $account){
            if($account->type == $accountType){
                return $account;
            }
        }
        return null;
    }

    public function getEmail($accountType){
        foreach($this->accounts as $account){
            if($account->type == $accountType){
                return $account->email;
            }
        }
        return null;
    }

    public function getUniqueEmails(){
        $emails = [];
        foreach($this->accounts as $account){
            $emails[$account->id] = $account->email;
        }
        return array_unique($emails);
    }

    public function getLastLoginTime(){
        $user_id = $this->id;

        $log = LoginData::whereHas('account', function($query) use ($user_id){
            $query->where('user_id', $user_id);
        })->orderBy('created_at', 'desc')->first();

        if($log==null){
            return "Žiadne prihlásenie";
        }else{
            return $log->timestamp;
        }
    }

    public function canAccessChatroom($chatroomId){
        $chatroom = Chatroom::findOrFail($chatroomId);
        if($chatroom->type == 'private'){
            if($this->chatrooms()->find($chatroomId)){
                return true;
            }
            return false;
        }
        return true;
    }

    public function accounts() {
        return $this->hasMany('App\Account', 'user_id');
    }

    public function comments() {
        return $this->hasMany('Modules\Forum\Entities\ForumComment', 'user_id');
    }

    public function threads() {
        return $this->hasMany('Modules\Forum\Entities\ForumThread', 'user_id');
    }

    public function categories() {
        return $this->hasMany('Modules\Forum\Entities\ForumCategorie', 'user_id');
    }

    public function groups() {
        return $this->hasMany('Modules\Forum\Entities\ForumGroup', 'user_id');
    }

    public function chatroomPermissions() {
        return $this->hasMany('Modules\Chat\Entities\Permission', 'user_id');
    }

    public function chatrooms() {
        return $this->belongsToMany('Modules\Chat\Entities\Chatroom', 'chatroom_permissions', 'user_id', 'chatroom_id');
    }

    public function regulators() {
        return $this->hasMany('Modules\Controller\Entities\Regulator', 'user_id');
    }

    public function reservations() {
        return $this->hasMany(Reservation::class);
    }

    public function deviceReserved(PhysicalDevice $physicalDevice) {
        return $this->reservations()->whereHas('physicalDevice', function($q) use ($physicalDevice) {
            $q->where('id', $physicalDevice->id);
        })->count() != 0;
    }
}
