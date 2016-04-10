<?php namespace Modules\Controller\Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Controller\Entities\Regulator;

class ControllerTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$regulator = new Regulator();
		$regulator->type = "local";
		$regulator->title = "Testovací regulátor";
		$regulator->body = 'y1=u1';
		$regulator->system_id = 1;
		$regulator->type = 'public';

		$user = User::where('name','Matej')->first();
		$regulator->user()->associate($user);

		$regulator->save();
	}

}