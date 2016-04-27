<?php namespace Modules\Reservation\Entities;
   
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\PhysicalDevice;
use Modules\Experiments\Entities\ServerExperiment;

class Reservation extends Model {

    protected $fillable = ['user_id','physical_device_id','start','end'];
    protected $with = ["physicalDevice"];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function physicalDevice()
    {
    	return $this->belongsTo(PhysicalDevice::class)->withTrashed();
    }

    public function scopeCollidingWith($query, Carbon $start, Carbon $end)
    {
    	return $query->where('start','>=', $start) // vnutri hranice
    	->where('end','<=',$end)->orWhere(function($query) use ($start, $end) {
    		$query->where('start','<',$start)->where('end','>',$start); // zlava prekryva
    	})->orWhere(function($query) use ($start, $end) {
    		$query->where('start','<',$end)->where('end','>',$end); //sprava prekryva
    	})->orWhere(function($query) use ($start, $end) {
    		$query->where('start','<',$start)->where('end','>',$end); // hranica je vnutri
    	});
    }

    public function scopeNotCollidingWith($query, Carbon $start, Carbon $end)
    {
    	return $query->where('start','<',$start)->where('end','<',$start)
    	->orWhere(function($query) use ($start, $end) {
    		$query->where('start','>',$end)->where('end','>',$end);
    	});
    }

    public function scopeCurrent($query)
    {
        $now = Carbon::now();
        return $query->where('start','<=', $now)->where('end','>=',$now);
    }

}