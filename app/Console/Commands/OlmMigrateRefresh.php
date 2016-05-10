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
        "Experiments",
        "Report",
        'Reservation',
        'Controller'
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
        $moduleNames = array_keys(Module::getOrdered());
        $reversedNames = array_reverse($moduleNames);

        // Since order of migrations depend on the order of moodules
        // we reverse the order of ordered module keys
        // in order to reset migrations in decending
        // order
        foreach ($reversedNames as $name) {
            $this->call("module:migrate-reset", ["module" => $name]);
        }
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
