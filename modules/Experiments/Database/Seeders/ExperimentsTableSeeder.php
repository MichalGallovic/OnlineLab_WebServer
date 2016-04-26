<?php namespace Modules\Experiments\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\Device;
use Modules\Experiments\Entities\Server;
use Modules\Experiments\Entities\Software;
use Modules\Experiments\Entities\Experiment;
use Modules\Experiments\Entities\PhysicalDevice;
use Modules\Experiments\Entities\ServerExperiment;
use Modules\Experiments\Entities\PhysicalExperiment;

class ExperimentsTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	
	protected $tables = [
		'devices',
		'softwares',
		'experiments',
		'servers',
		'physical_devices',
		'physical_experiment'
	];

	public function run()
	{
		Model::unguard();

		$this->cleanDatabase();
		
		$server100 = Server::create([
			"name"	=>	"s1",
			"ip"	=>	"192.168.100.100",
			"port"	=>	"80",
			"production"	=>	1,
			"database"		=>	1,
			"reachable"		=>	1
		]);

		$server200 = Server::create([
			"name"	=>	"s2",
			"ip"	=>	"192.168.100.200",
			"port"	=>	"80",
			"production"	=>	1,
			"database"		=>	1,
			"reachable"		=>	1
		]);

		$device = Device::create([
			"name"	=>	"tos1a"
		]);

		$ledDevice = Device::create([
			"name"	=>	"led_cube"
		]);

		PhysicalDevice::create([
			'server_id' => $server100->id,
			'device_id' => $device->id,
			'name' => str_random(3),
			'status' => 'ready'
		]);

		PhysicalDevice::create([
			'server_id' => $server200->id,
			'device_id' => $ledDevice->id,
			'name' => str_random(3),
			'status' => 'ready'
		]);

		Software::create([
			"name" => "openloop"
		]);
		Software::create([
			"name" => "matlab"
		]);
		Software::create([
			"name" => "scilab"
		]);
		Software::create([
			"name" => "openmodelica"
		]);


		$devices = Device::all();
		$softwares = Software::all();

		foreach ($devices as $device) {
			foreach ($softwares as $software) {
				$experiment = new Experiment;
				$experiment->device()->associate($device);
				$experiment->software()->associate($software);
				$experiment->save();
			}
		}

		$servers = Server::all();
		$experiments = Experiment::all();
		$physicalDevices = PhysicalDevice::all();

		foreach ($servers as $server) {
			foreach ($experiments as $experiment) {
				$instance = PhysicalExperiment::create([
					"server_id" 	=>	$server->id,
					"experiment_id"	=>	$experiment->id,
					'physical_device_id'	=>	$physicalDevices->random()->id
				]);
				
			}
		}


	}

	private function cleanDatabase()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0');

		foreach($this->tables as $table)
		{
			DB::table($table)->truncate();
		}

		DB::statement('SET FOREIGN_KEY_CHECKS=1');
	} 

}