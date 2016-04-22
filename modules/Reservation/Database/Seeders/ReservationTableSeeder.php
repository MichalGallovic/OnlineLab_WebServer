<?php namespace Modules\Reservation\Database\Seeders;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Reservation\Entities\Reservation;
use Modules\Experiments\Entities\ServerExperiment;

class ReservationTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();
			
		$users = User::all();
		$experimentInstances = ServerExperiment::all();

		foreach (range(0,100) as $i) {
			$user = $users->random();
			$instance = $experimentInstances->random();
			$start = Carbon::now()->addWeeks(rand(0,10))->addMinutes(rand(1,20)*20);
			$seconds = $start->second;
			$minutes = $start->minute;
			$start = $start->subMinutes($minutes%10)->subSeconds($seconds);
			$end = $start;
			$start = $start->toDateTimeString();
			$end = $end->addMinutes(rand(1,20)*10);


			$reservation = Reservation::where('experiment_server_id',$instance->id)
			->where('start','>=',$start)->where('end','<=',$end)->firstOrCreate([
					"experiment_server_id" => $instance->id,
					"user_id" => $user->id,
					'start' => $start,
					'end' => $end
				]);
		}
	}

}