<?php

namespace Modules\Controller\Transformers;

use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use Modules\Controller\Entities\Schema;
use Modules\Experiments\Entities\Experiment;


class SchemaTransformer extends TransformerAbstract
{


	public function transform(Schema $schema)
	{
		$user = Auth::user()->user;

		if($user->role == 'admin') {
			$regulators = $schema->regulators;
		} else {
			$regulators = $schema->regulators()->public()->orOfUser($user)->get();
		}

		$regulators = $regulators->map(function($regulator) {
			$regulator = [
				"name" => $regulator->title,
				'url' => url('api/regulators',[$regulator->id]),
				'id'  => $regulator->id
			];
			return $regulator;
		});



		return [
			"name"	=>	$schema->title,
			"url"	=>	url('api/schemas',[$schema->id]),
			"regulators" => $regulators,
			'id' => $schema->id
		];
	}
}