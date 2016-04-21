<?php namespace Modules\Reservation\Entities;
   
use App\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\ServerExperiment;

class Reservation extends Model {

    protected $fillable = [];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function experimentInstance()
    {
    	return $this->belongsTo(ServerExperiment::class);
    }

}