<?php namespace Modules\Report\Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;
use Modules\Report\Entities\Report;
use Illuminate\Database\Eloquent\Model;
use Modules\Experiments\Entities\ServerExperiment;

class ReportTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();
		$instances = ServerExperiment::all();

		$users = User::all();

		foreach(range(0,100) as $i) {
			$instance = $instances->random();
			Report::create([
				"experiment_server_id"	=>	$instance->id,
				"user_id"	=>	$users->random()->id,
				"filled"	=>	rand(0,1)
			]);
		}
	}

}