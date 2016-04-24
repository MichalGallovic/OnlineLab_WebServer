<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class OlmMigrateRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'olm:migrate-refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh all migrations';

    protected $modulesToSeed = [
        "Experiments",
        // "Report",
        'Reservation'
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call("module:migrate-reset");
        $this->call("migrate:refresh");
        $this->call("module:migrate");
        $this->call("db:seed");
        foreach ($this->modulesToSeed as $module) {
            $this->call("module:seed",["module" => $module]);   
        }
    }
}
