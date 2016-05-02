<?php

namespace Modules\Experiments\Jobs;

use App\User;
use App\Jobs\Job;
use Illuminate\Support\Arr;
use App\Services\ReportService;
use App\Services\ExperimentValidator;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Classes\ApplicationServer\Server;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Experiments\Entities\Experiment;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Modules\Experiments\Jobs\RunExperimentJob;
use Modules\Experiments\Entities\PhysicalDevice;
use Modules\Experiments\Entities\ServerExperiment;
use Modules\Experiments\Entities\PhysicalExperiment;
use Illuminate\Contracts\Validation\ValidationException;
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
        var_dump($this->experiment->device->name . " " . $this->experiment->software->name . " queued!");
        $instanceName = Arr::get($this->input,"instance");

        if($instanceName) {
            $physicalDevice = PhysicalDevice::ready()->ofDevice($this->input['device'])->ofName($this->input['instance'])->first();
        } else {
            $physicalDevice = PhysicalDevice::ready()->ofDevice($this->input['device'])->first();
        }

        if($physicalDevice) {
            $physicalExperiment = PhysicalExperiment::where('experiment_id', $this->experiment->id)->where('physical_device_id', $physicalDevice->id)->first();

            $validator = new ExperimentValidator($physicalExperiment->rules->toArray(), $this->input['input']);

            if($validator->fails()) {
                return false;
                // throw new ValidationException($validator->errors());
            }

            $report = new ReportService();
            $reportId = $report->create($this->user, $physicalExperiment, $this->input);
            $this->input = array_merge($this->input, [
                    "report_id" => $reportId
                ]);


            $server = new Server($physicalDevice->server->ip);
            $server->queueExperiment($this->input);
            if($server->success()) {
                $physicalDevice->status = "experimenting";
                $physicalDevice->save();
            }
        } else {
            $job = (new RunExperimentJob($this->user, $this->experiment, $this->input))->delay(5);
            $this->dispatch($job);
        }
    }

}