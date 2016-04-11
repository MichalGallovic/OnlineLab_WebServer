<?php namespace Modules\Controller\Entities;
   
use Illuminate\Database\Eloquent\Model;

class Regulator extends Model {

    protected $fillable = ['user_id', 'type', 'body', 'system_id', 'title'];

    public function user() {
        return $this->belongsTo('App\User','user_id');
    }

}