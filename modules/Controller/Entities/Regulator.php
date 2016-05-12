<?php namespace Modules\Controller\Entities;
   
use Illuminate\Database\Eloquent\Model;

class Regulator extends Model {

    protected $fillable = ['user_id', 'type', 'body', 'experiment_id', 'title'];

    public function getFilePath() {
        if($this->filename){
            return storage_path() . '/user_uploads/'.$this->user->id.'/regulators/'.$this->id. '/' . $this->filename;;
        }
        return storage_path() . '/user_uploads/'.$this->user->id.'/regulators/'.$this->id. '/' . $this->name.'.txt';
    }

    public function user() {
        return $this->belongsTo('App\User','user_id');
    }

    public function schema() {
        return $this->belongsTo('Modules\Controller\Entities\Schema','schema_id');
    }

}