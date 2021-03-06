<?php namespace Modules\Experiments\Entities;
   
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
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

    public function getRulesAttribute()
    {
        return (new Collection($this->commands))->map(function($command) {
            return (new Collection($command))->map(function($input) {
                $rule = Arr::get($input,'rules','');
                if(!empty($rule)) {
                    return [
                        $input["name"] => $rule
                    ];
                } 
                return [];
            })->filter(function($input) {
                return count($input) > 0;
            })->collapse()->toArray();
        });
    }

    public function server()
    {
    	return $this->belongsTo(Server::class);
    }

    public function physicalDevice()
    {
    	return $this->belongsTo(PhysicalDevice::class);
    }

    public function scopeRunnable($query)
    {
        return $query->whereNotNull('experiment_commands');
    }

    public function scopeReservable($query)
    {
        return $query->runnable()->whereNotNull('commands');
    }
}