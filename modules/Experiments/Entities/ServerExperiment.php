<?php namespace Modules\Experiments\Entities;
   
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\Experiment;

class ServerExperiment extends Model {

	protected $table = "experiment_server";
    protected $fillable = ["server_id","experiment_id"];
    protected $casts = [
    	"commands" => "array",
    	"experiment_commands" => "array",
    	"output_arguments" => "array"
    ];

    public function experiment()
    {
    	return $this->belongsTo(Experiment::class);
    }

    public function server()
    {
    	return $this->belongsTo(Server::class);
    }

    public function scopeHasExperiments($query)
    {
        return $this->where('instances','>',0);
    }

}