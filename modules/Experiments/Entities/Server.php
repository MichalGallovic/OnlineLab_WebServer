<?php namespace Modules\Experiments\Entities;
   
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\Experiment;

class Server extends Model {

    protected $fillable = ["name","ip","port"];

    public function experiments()
    {
    	return $this->belongsToMany(Experiment::class)->withPivot('available');
    }

}