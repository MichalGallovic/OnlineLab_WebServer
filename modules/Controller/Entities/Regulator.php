<?php namespace Modules\Controller\Entities;
   
use App\User;
use Illuminate\Database\Eloquent\Model;

class Regulator extends Model {

    protected $fillable = ['user_id', 'type', 'body', 'experiment_id', 'title'];

    public function getFilePath() {
        return storage_path() . '/user_uploads/'.$this->user->id.'/regulators/'.$this->id. '/' . $this->filename;;
    }

    public function user() {
        return $this->belongsTo('App\User','user_id');
    }

    public function schema() {
        return $this->belongsTo('Modules\Controller\Entities\Schema','schema_id');
    }

    public function scopePublic($query)
    {
        return $query->where('type','public');
    }

    public function scopeOrOfUser($query, User $user)
    {
        return $query->orWhere('user_id', $user->id);
    }
}