<?php namespace Modules\Controller\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Controller\Entities\Schema;

class SchemaTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$schema = new Schema();
		$schema->title = "Testovacia schéma";
		$schema->image = "schema1.png";
		$schema->filename = "schema1.txt";
		$schema->type = "text";
		$schema->software = "matlab";
		$schema->save();

	}

}