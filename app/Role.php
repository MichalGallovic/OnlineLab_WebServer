<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = "olm_account_types";

    public function users() {
        return $this->hasMany('App\User');
    }
}
