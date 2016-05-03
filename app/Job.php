<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
	public static function retryAll()
	{
		$jobs = new static;
		$jobs->all()->each(function($job) {
			$job->available_at = time() + 10;
			$job->save();
		});
	}
}
