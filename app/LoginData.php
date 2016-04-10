<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginData extends Model
{
    protected $table = "account_accesses";
    public $timestamps = false;

    protected $fillable = ['account_id', 'ip', 'os'];

    public function account() {
        return $this->belongsTo('App\Account','account_id');
    }
}
