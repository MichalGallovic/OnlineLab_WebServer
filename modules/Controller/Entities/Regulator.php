<?php namespace Modules\Controller\Entities;
   
use Illuminate\Database\Eloquent\Model;

class Regulator extends Model {

    protected $fillable = ['user_id', 'type', 'body', 'system_id', 'title'];

    public function getFilePath() {
        return storage_path() . '/user_uploads/'.$this->user->id.'/regulators/' . $this->filename;;
    }

    public function user() {
        return $this->belongsTo('App\User','user_id');
    }

    public function schema() {
        return $this->belongsTo('Modules\Controller\Entities\Schema','schema_id');
    }

}