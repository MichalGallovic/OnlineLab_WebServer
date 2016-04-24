<?php namespace Modules\Reservation\Database\Seeders;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Reservation\Entities\Reservation;
use Modules\Experiments\Entities\PhysicalDevice;
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
		$physicalDevices = PhysicalDevice::all();

		foreach (range(0,1000) as $i) {
			$user = $users->random();
			$physicalDevice = $physicalDevices->random();
			$start = Carbon::now()->addWeeks(rand(0,10))->addMinutes(rand(1,20)*20);
			$seconds = $start->second;
			$minutes = $start->minute;
			$start = $start->subMinutes($minutes%10)->subSeconds($seconds);
			$end = $start;
			$start = $start->toDateTimeString();
			$end = $end->addMinutes(rand(1,20)*10);

			$reservation = Reservation::where('physical_device_id',$physicalDevice->id)
				->collidingWith(new Carbon($start), new Carbon($end))->firstOrCreate([
					"physical_device_id" => $physicalDevice->id,
					"user_id" => $user->id,
					'start' => $start,
					'end' => $end
				]);
		}
	}

}