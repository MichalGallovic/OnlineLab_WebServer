<?php namespace Modules\Experiments\Entities;
   
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\Experiment;

class ServerExperiment extends Model {

	protected $table = "experiment_server";
    protected $fillable = ["server_id","experiment_id","device_name","status"];
    protected $casts = [
    	"commands" => "array",
    	"experiment_commands" => "array",
    	"output_arguments" => "array"
    ];
    protected $with = ["experiment","server"];

    public function experiment()
    {
    	return $this->belongsTo(Experiment::class);
    }

    public function server()
    {
    	return $this->belongsTo(Server::class);
    }

    public function offline()
    {
        return $this->status == "offline";
    }

    public function scopeAvailable($query)
    {
        return $query->where('status','!=',"offline")->whereHas('server', function($q) {
            $q->available();
        })->whereHas('experiment', function($q) {
            $q->available();
        });
    }

    public function scopeAvailableForExperiment($query)
    {
        return $query->where('status','=',"ready")->whereHas('server', function($q) {
            $q->available();
        });
    }

    public function scopeOfInstance($query, $instanceName)
    {
        return $query->where('device_name', $instanceName);
    }

}