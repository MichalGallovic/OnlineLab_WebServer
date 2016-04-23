<?php namespace Modules\Experiments\Entities;
   
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\Device;
use Modules\Experiments\Entities\Server;

class PhysicalDevice extends Model {

    protected $fillable = ["server_id","device_id","status","name"];

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
    	return $this->belongsToMany(Experiment::class,'experiment_server');
    }

}