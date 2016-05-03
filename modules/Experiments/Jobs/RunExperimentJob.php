<?php

namespace Modules\Experiments\Jobs;

use App\User;
use App\Jobs\Job;
use Illuminate\Support\Arr;
use App\Services\ReportService;
use App\Services\ExperimentRunner;
use App\Services\ExperimentValidator;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Classes\ApplicationServer\Server;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Experiments\Entities\Experiment;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Exceptions\Experiments\DeviceNotReady;
use Modules\Experiments\Jobs\RunExperimentJob;
use Modules\Experiments\Entities\PhysicalDevice;
use Modules\Experiments\Entities\ServerExperiment;
use Modules\Experiments\Entities\PhysicalExperiment;
use Illuminate\Contracts\Validation\ValidationException;
use App\Exceptions\Experiments\DeviceReservedForThisTime;
/**
* Run experiment job
*/
class RunExperimentJob extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

    protected $experiment;
    protected $input;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Experiment $experiment, array $input)
    {
    	$this->experiment = $experiment;
        $this->input = $input;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try {
            $runner = new ExperimentRunner(
                $this->user, $this->experiment, $this->input
            );
            $runner->queue();
        } catch(DeviceNotReady $e) {
            // The experiment should be postponed
            // $e->nextTrySeconds();
            var_dump("not ready");
            $job = (new RunExperimentJob($this->user, $this->experiment, $this->input))->delay(5);
            $this->dispatch($job);
        } catch(DeviceReservedForThisTime $e) {
            var_dump("reserved");
            $job = (new RunExperimentJob($this->user, $this->experiment, $this->input))->delay($e->nextTrySeconds());
            $this->dispatch($job);
        }
    }

}