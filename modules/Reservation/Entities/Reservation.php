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
    	return $query->where(function($query) use ($start, $end) {
            $query->where(function($query) use ($start, $end) {
                $query->where('start','>=', $start)->where('end','<=',$end); // vnutri hranice
            })->orWhere(function($query) use ($start, $end) {
                $query->where('start','<',$start)->where('end','>',$start); // zlava prekryva
            })->orWhere(function($query) use ($start, $end) {
                $query->where('start','<',$end)->where('end','>',$end); //sprava prekryva
            })->orWhere(function($query) use ($start, $end) {
                $query->where('start','<',$start)->where('end','>',$end); // hranica je vnutri
            });
        });
    }

    public function scopeNotCollidingWith($query, Carbon $start, Carbon $end)
    {
    	return $query->where('start','<',$start)->where('end','<',$start)
    	->orWhere(function($query) use ($start, $end) {
    		$query->where('start','>',$end)->where('end','>',$end);
    	});
    }

    public function scopeToday($query)
    {
        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();
        return $query->where('start', '>=', $startOfDay)->where('end','<=',$endOfDay);   
    }

    public function scopeEndAfterNow($query)
    {
        $now = Carbon::now();
        return $query->where('end','>=',$now);
    }

    public function scopeForDay($query, Carbon $day)
    {
        $startOfDay = $day->copy()->startOfDay();
        $endOfDay = $day->copy()->endOfDay();
        return $query->where('start', '>=', $startOfDay)->where('end','<=',$endOfDay);   
    }

    public function scopeHasExistingDevice($query)
    {
        return $query->whereHas('physicalDevice', function($q) {
            $q->whereNull('deleted_at')->ready();
        });
    }

    public function scopeCurrent($query)
    {
        $now = Carbon::now();
        return $query->where('start','<=', $now)->where('end','>=',$now);
    }

}