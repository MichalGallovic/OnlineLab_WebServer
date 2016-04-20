<?php

namespace Modules\Experiments\Jobs;

use App\Jobs\Job;
use Illuminate\Support\Arr;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Classes\ApplicationServer\Server;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Experiments\Entities\Experiment;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Modules\Experiments\Jobs\RunExperimentJob;
use Modules\Experiments\Entities\ServerExperiment;
/**
* Run experiment job
*/
class RunExperimentJob extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

    protected $experiment;
    protected $input;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Experiment $experiment, array $input)
    {
    	$this->experiment = $experiment;
        $this->input = $input;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        var_dump($this->experiment->device->name . " " . $this->experiment->software->name . " queued!");
        $instanceName = Arr::get($this->input,"instance");

        if($instanceName) {
            $instance = ServerExperiment::availableForExperiment()->ofInstance($instanceName)->first();
        } else {
            $instance = ServerExperiment::availableForExperiment()->first();
        }

        if($instance) {
            $server = new Server($instance->server->ip);
            $server->queueExperiment($this->input);

            $instance->status = "experimenting";
            $instance->save();
        } else {
            $job = (new RunExperimentJob($this->experiment, $this->input))->delay(5);
            $this->dispatch($job);
        }
    }

}