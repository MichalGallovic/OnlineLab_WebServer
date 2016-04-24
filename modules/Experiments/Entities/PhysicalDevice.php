<?php namespace Modules\Experiments\Entities;
   
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\Device;
use Modules\Experiments\Entities\Server;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhysicalDevice extends Model {

    use SoftDeletes;

    protected $fillable = ["server_id","device_id","status","name"];
    protected $dates = ["deleted_at"];

    public function server()
    {
    	return $this->belongsTo(Server::class);
    }

    public function device()
    {
    	return $this->belongsTo(Device::class);
    }

    public function experiments()
    {
    	return $this->belongsToMany(Experiment::class,'physical_experiment')->whereNull('physical_experiment.deleted_at');
    }

    public function scopeReady($query)
    {
        return $query->where('status','ready');
    }

    public function scopeOfDevice($query, $deviceName)
    {
        return $query->whereHas('device', function($q) use ($deviceName) {
            $q->where('name', $deviceName);
        });
    }

    public function scopeOfName($query, $name)
    {
        return $query->where('name', $name);
    }

}