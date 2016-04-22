<?php namespace Modules\Reservation\Entities;
   
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\ServerExperiment;

class Reservation extends Model {

    protected $fillable = ['user_id','experiment_server_id','start','end'];
    protected $with = ["experimentInstance"];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function experimentInstance()
    {
    	return $this->belongsTo(ServerExperiment::class,'experiment_server_id');
    }

    public function scopeCollidingWith($query, Carbon $start, Carbon $end)
    {
    	return $query->where('start','>=', $start)
    	->where('end','<=',$end)->orWhere(function($query) use ($start, $end) {
    		$query->where('start','<=',$start)->where('end','>=',$end);
    	})->orWhere(function($query) use ($start, $end) {
    		$query->where('start','<=',$end)->where('end','>=',$end);
    	})->orWhere(function($query) use ($start, $end) {
    		$query->where('start','<=',$start)->where('end','>=',$start);
    	})->orWhere(function($query) use ($start, $end) {
    		$query->where('start','<=',$start)->where('end','>=',$end);
    	});

    	// return $query->whereBetween('start',[$start, $end])
    	// ->orWhere(function($query) use ($start, $end) {
    	// 	$query->whereBetween('end', [$start, $end]);
    	// });
    }

    public function scopeNotCollidingWith($query, Carbon $start, Carbon $end)
    {
    	return $query->where('start','<',$start)->where('end','<',$start)
    	->orWhere(function($query) use ($start, $end) {
    		$query->where('start','>',$end)->where('end','>',$end);
    	});
    }

}