<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Caffeinated\Modules\Facades\Module;

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
        // "Experiments",
        // "Report",
        // 'Reservation',
        // 'Controller'
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
        $this->call("module:migrate-reset", ["module" => "Reservation"]);
        $this->call("module:migrate-reset", ["module" => "Report"]);
        $this->call("module:migrate-reset", ["module" => "Forum"]);
        $this->call("module:migrate-reset", ["module" => "Chat"]);
        $this->call("module:migrate-reset", ["module" => "Controller"]);
        $this->call("module:migrate-reset", ["module" => "Experiments"]);

        $this->call("migrate:refresh");

        $this->call("module:migrate", ["module" => "Experiments"]);
        $this->call("module:migrate", ["module" => "Controller"]);
        $this->call("module:migrate", ["module" => "Chat"]);
        $this->call("module:migrate", ["module" => "Forum"]);
        $this->call("module:migrate", ["module" => "Report"]);
        $this->call("module:migrate", ["module" => "Reservation"]);

        $this->call("db:seed");
        foreach ($this->modulesToSeed as $module) {
            $this->call("module:seed",["module" => $module]);   
        }
    }
}
