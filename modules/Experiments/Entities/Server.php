<?php namespace Modules\Experiments\Entities;
   
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\Experiment;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Experiments\Entities\PhysicalDevice;

class Server extends Model {
    
    use SoftDeletes;
    
    protected $fillable = ["name","ip","port","color", "production"];
    protected $dates = ["deleted_at"];

    public function experiments()
    {
    	return $this->belongsToMany(Experiment::class,'physical_experiment')->whereNull('physical_experiment.deleted_at');
    }

    public function physicalDevices()
    {
        return $this->hasMany(PhysicalDevice::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('production', true)
        ->where('disabled', false)->where('database', true)
        ->where('reachable', true);
    }

    public function isAvailable()
    {
        return !$this->disabled && $this->database && $this->reachable;
    }

    public function scopeHasExperiments($query)
    {
        return $query->has('experiments');
    }

    public function scopeHasInstances($query, $instanceName)
    {
        return $query->where("experiment_server.device_name", $instanceName);
    }

}