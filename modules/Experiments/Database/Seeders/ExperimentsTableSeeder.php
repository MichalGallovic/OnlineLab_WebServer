<?php namespace Modules\Experiments\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\Device;
use Modules\Experiments\Entities\Server;
use Modules\Experiments\Entities\Software;
use Modules\Experiments\Entities\Experiment;

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
		'experiment_server'
	];

	public function run()
	{
		Model::unguard();

		$this->cleanDatabase();
		
		Server::create([
			"name"	=>	"s1",
			"ip"	=>	"192.168.100.100",
			"port"	=>	"80"
		]);

		Server::create([
			"name"	=>	"s2",
			"ip"	=>	"192.168.100.200",
			"port"	=>	"80"
		]);

		Device::create([
			"name"	=>	"tos1a"
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
				$experiment->software()->associate($software)->save();
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