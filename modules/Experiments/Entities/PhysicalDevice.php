<?php namespace Modules\Experiments\Entities;
   
use Carbon\Carbon;
use Pingpong\Modules\Facades\Module;
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\Device;
use Modules\Experiments\Entities\Server;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Reservation\Entities\Reservation;

class PhysicalDevice extends Model {

    use SoftDeletes;

    protected $fillable = ["server_id","device_id","status","name"];
    protected $dates = ["deleted_at"];
    protected $with = ['server'];

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

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function scopeReady($query)
    {
        return $query->where('status','ready');
    }

    public function scopeNotReserved($query, $additionalSeconds = 0)
    {
        $beforeReservation = intval(Module::get('Experiments')->settings("before_reservation"));
        $beforeReservation += $additionalSeconds;
        
        $busyTime = Carbon::now()->addSeconds($beforeReservation);

        return $query->whereHas('reservations', function($q) use ($busyTime) {
            $q->where('start','<=', $busyTime);
        },'=',0);
    }

    public function scopeOnline($query)
    {
        return $query->where('status','!=','offline');
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