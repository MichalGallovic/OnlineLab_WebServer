<?php namespace Modules\Experiments\Entities;
   
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\Server;
use Modules\Experiments\Entities\Experiment;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Experiments\Entities\PhysicalDevice;

class PhysicalExperiment extends Model {

    use SoftDeletes;

	protected $table = "physical_experiment";
    protected $fillable = [
    	"server_id",
    	"experiment_id",
    	"physical_device_id"
    ];
    protected $casts = [
    	"commands" => "array",
    	"experiment_commands" => "array",
    	"output_arguments" => "array"
    ];
    protected $with = ["experiment","server","physicalDevice"];
    protected $dates = ["deleted_at"];

    public function experiment()
    {
    	return $this->belongsTo(Experiment::class);
    }

    public function server()
    {
    	return $this->belongsTo(Server::class);
    }

    public function physicalDevice()
    {
    	return $this->belongsTo(PhysicalDevice::class);
    }
}