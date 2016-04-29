<?php namespace Modules\Controller\Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Controller\Entities\Regulator;
use Modules\Controller\Entities\Schema;
use Modules\Experiments\Entities\Experiment;

class ControllerTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */

	public function run()
	{
		Model::unguard();

		//$this->call(SchemaTableSeeder::class);

		$experiment = Experiment::first();

		$schema = new Schema();
		$schema->title = "Testovacia schéma";
		$schema->image = "schema1.png";
		$schema->filename = "schema1.txt";
		$schema->type = "text";
		$schema->experiment()->associate($experiment);
		$schema->save();

		$regulator = new Regulator();
		$regulator->type = "local";
		$regulator->title = "Testovací regulátor";
		$regulator->body = 'y1=u1';

		$regulator->type = 'public';

		$user = User::first();
		$regulator->user()->associate($user);

		$schema = Schema::first();
		$regulator->schema()->associate($schema);

		$regulator->save();
	}

}