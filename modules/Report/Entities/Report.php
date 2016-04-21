<?php namespace Modules\Report\Entities;
   
use App\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\ServerExperiment;

class Report extends Model {

    protected $fillable = [];
    protected $casts = [
	    "input" => "array",
	    "output" => "array"
    ];

    public function experimentInstance()
    {
    	return $this->belongsTo(ServerExperiment::class,"experiment_server_id");
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

}