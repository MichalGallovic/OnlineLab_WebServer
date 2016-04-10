<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Account extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $table = "accounts";

    public $timestamps = false;
    public $firstLogin = true;

    protected $fillable = ['user_id', 'email', 'type', 'login_id', 'type', 'confirmation_code', 'notify'];
    protected $hidden = ['remember_token'];

    public function user() {
        return $this->belongsTo('App\User','user_id');
    }

    public function logins() {
        return $this->hasMany('App\LoginData', 'account_id');
    }
}
