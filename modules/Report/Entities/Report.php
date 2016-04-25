<?php namespace Modules\Report\Entities;
   
use App\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\ServerExperiment;
use Modules\Experiments\Entities\PhysicalExperiment;

class Report extends Model {

    protected $fillable = [];
    protected $casts = [
	    "input" => "array",
	    "output" => "array"
    ];

    public function physicalExperiment()
    {
    	return $this->belongsTo(PhysicalExperiment::class)->withTrashed();
    }

    public function physicalDevice()
    {
        $physicalExperiment = $this->physicalExperiment;

        return $physicalExperiment->physicalDevice()->withTrashed();
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function scopeOfUser($query, $user)
    {
        if($user->role == 'admin') return $query;

        return $query->where('user_id', $user->id);
    }

}