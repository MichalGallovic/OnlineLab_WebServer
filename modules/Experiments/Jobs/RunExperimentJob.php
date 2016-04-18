<?php

namespace Modules\Experiments\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Classes\ApplicationServer\Server;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Experiments\Entities\Experiment;
use Modules\Experiments\Entities\ServerExperiment;
use Illuminate\Foundation\Bus\DispatchesJobs;
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
        $availableServer = $this->experiment->servers()->available()->freeExperiment()->first();

        if(!$availableServer) {
            $this->dispatch(new RunExperimentJob($this->experiment, $this->input));
        }

     	$server = new Server($availableServer->ip);
     	$server->queueExperiment($this->input);

        $serverExperiment = ServerExperiment::where('server_id', $availableServer->id)
        ->where('experiment_id', $this->experiment->id)->first();

        $serverExperiment->free_instances = $serverExperiment->free_instances - 1;
        $serverExperiment->save();
    }

}