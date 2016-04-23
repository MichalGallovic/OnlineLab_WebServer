<?php namespace Modules\Experiments\Entities;
   
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\Device;
use Modules\Experiments\Entities\Server;
use Modules\Experiments\Entities\Software;

class Experiment extends Model {

    protected $fillable = ["software_id", "device_id"];
    protected $with = ["device","software"];

    public function device() {
    	return $this->belongsTo(Device::class);
    }

    public function software() {
    	return $this->belongsTo(Software::class);
    }

    public function servers() {
    	return $this->belongsToMany(Server::class,'physical_experiment');
    }

    public function physicalDevices()
    {
        return $this->belongsToMany(PhysicalDevice::class,'physical_experiment');
    }

    public function scopeOfDevice($query, $device)
    {
        return $query->whereHas('device', function($q) use ($device) {
            $q->where('name', $device);
        });
    }

    public function scopeOfSoftware($query, $software)
    {
        return $query->whereHas('software', function($q) use ($software) {
            $q->where('name', $software);
        });
    }

}