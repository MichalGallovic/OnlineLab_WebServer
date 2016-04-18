<?php namespace Modules\Experiments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QueueExperimentRequest extends FormRequest {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			"device" 	=>	"required|string",
			"software"	=>	"required|string",
			"input"		=>	"required|array"
		];
	}

}
