<?php namespace Modules\Experiments\Entities;
   
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\PhysicalDevice;

class Device extends Model {

    protected $fillable = ["name"];

    public function physicalDevices()
    {
    	return $this->hasMany(PhysicalDevice::class);
    }

}