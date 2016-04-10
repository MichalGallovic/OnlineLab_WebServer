<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    protected $table = "olm_account_types";

    public function accounts() {
        return $this->hasMany('App\Account');
    }
}
