<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

/**
* Experiment validator
*/
class ExperimentValidator
{
	protected $rules;
	protected $commands;
	protected $validator;

	public function __construct($rules, $commands)
	{
		$this->rules = $rules;
		$this->commands = $commands;
		// $this->validator = Validator::make($this->input, $this->rules);
	}

	public function fails()
	{
		$fails = false;
		$errors = [];
		foreach ($this->commands as $name => $inputs) {
			$validator = Validator::make($inputs, $this->rules[$name]);
			if($validator->fails()) {
				$this->validator = $validator;
				return $validator->fails();
			}
		}

		return false;
	}

	public function errors()
	{
		return $this->validator->errors();
	}
	
}