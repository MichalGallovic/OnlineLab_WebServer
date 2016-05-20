<?php namespace Modules\Statistics\Http\Controllers;

use App\Account;
use App\LoginData;
use Modules\Chat\Entities\Message;
use Modules\Experiments\Entities\Experiment;
use Pingpong\Modules\Routing\Controller;
use Carbon\Carbon;
use Modules\Experiments\Entities\Device;
use DB;

class StatisticsController extends Controller {
	
	public function index()
	{

		//traffic
		$traffic = [];

		$accesses = LoginData::select('created_at', 'id')
			->get()
			->groupBy(function($date) {
				return Carbon::parse($date->created_at)->format('H');
			});

		for($i=0; $i<24; $i++){
			if($accesses[sprintf('%02d', $i)]){
				$traffic[$i] = sizeof($accesses[sprintf('%02d', $i)]);
			}else{
				$traffic[$i] = 0;
			}
		}

		//tagCloud
		$words = [];
		$tagCloud = [];

		foreach (Message::lists('body') as $message) {
			foreach (explode(" ",$message) as $word){
				$key = str_replace(array(':', '\\', '/', '*'), ' ', $word);
				if(strlen($key) > 4){
					if(array_key_exists($key, $words)){
						$words[$key]++;
					}else{
						$words[$key] = 1;
					}
				}
			}
		}

		foreach ($words as $word=>$weight) {
			array_push($tagCloud, ['text' => $word, 'weight' => $weight]);
		}

		usort($tagCloud, function($a, $b)
		{
			if ($a==$b) return 0;
			return ($a['weight']<$b['weight'])?1:-1;
		});

		$tagCloud = array_slice($tagCloud,0,40);

		$items = LoginData::all();

		//accounts
		$accounts =  Account::select('type', DB::raw('count(*) as count'))
			->groupBy('type')
			->get();
		$accountLabels = [];
		$accountData = [];

		foreach ($accounts as $account) {
			array_push($accountLabels, $account->type);
			array_push($accountData, $account->count);
		}

		//experiments
/*
		return $experiments = Experiment::join('softwares', 'software_id', '=', 'softwares.id')
			->select('softwares.name as enviroment', 'physical_devices.name as physical', 'devices.name as devicename', DB::raw('count(experiments.id) as total'), DB::raw('count(physical_experiment.id) as physical_total'))
			->join('devices', 'device_id', '=', 'devices.id')
			->join('physical_devices', 'physical_devices.device_id', '=', 'devices.id')
			->join('physical_experiment', 'physical_experiment.experiment_id', '=', 'experiments.id')
			->groupby('softwares.id', 'experiments.id')
			->get();
*/

/*		$experiments = Device::select('experiments.id as experiment', 'devices.name as device', 'softwares.name as enviroment', DB::raw('count(physical_experiment.id) as total')) //
			->join('experiments', 'experiments.device_id', '=', 'devices.id')
			->join('softwares', 'experiments.software_id', '=', 'softwares.id')
			->join('physical_experiment', 'physical_experiment.experiment_id', '=', 'experiments.id')
			->groupby('devices.id', 'softwares.id')
			->get();
*/
		$experiments = Device::select('experiments.id as experiment', 'devices.name as device', DB::raw('count(physical_experiment.id) as total')) //
			->join('experiments', 'experiments.device_id', '=', 'devices.id')
			->join('physical_experiment', 'physical_experiment.experiment_id', '=', 'experiments.id')
			->groupby('devices.id')
			->get();

		//map



		return view('statistics::index', compact('items', 'tagCloud', 'traffic', 'accountLabels', 'accountData', 'experiments'));

	}




	
}