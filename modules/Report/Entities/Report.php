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
    	return $this->belongsTo(PhysicalExperiment::class);
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

}