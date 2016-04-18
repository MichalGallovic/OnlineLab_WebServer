<?php namespace Modules\Experiments\Entities;
   
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\Experiment;

class Server extends Model {

    protected $fillable = ["name","ip","port","color", "production"];

    public function experiments()
    {
    	return $this->belongsToMany(Experiment::class)->withPivot('instances');
    }

    public function sumExperimentInstances()
    {	
    	$experiments = $this->experiments;
    	$count = 0;
    	foreach ($experiments as $experiment) {
    		$count += $experiment->pivot->instances;
    	}
    	return $count;
    }

    public function scopeAvailable($query)
    {
        return $query->where('production', true)
        ->where('available', true)->where('disabled', false)
        ->where('database', true)->where('reachable', true)
        ->where('queue', true);
    }

    public function scopeHasExperiments($query)
    {
        return $query->where('experiment_server.instances','>',0);
    }

    public function scopeFreeExperiment($query)
    {
        return $query->where("experiment_server.free_instances",'>',0);
    }

}