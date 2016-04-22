<?php namespace Modules\Reservation\Entities;
   
use App\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\ServerExperiment;

class Reservation extends Model {

    protected $fillable = ['user_id','experiment_server_id','start','end'];
    protected $with = ["experimentInstance"];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function experimentInstance()
    {
    	return $this->belongsTo(ServerExperiment::class,'experiment_server_id');
    }

}