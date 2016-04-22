<?php namespace Modules\Reservation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest {

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
			'device'	=>	'required|string',
			'software'	=>	'required|string',
			'instance'	=>	'required|string',
			'start'		=>	'required|date',
			'end'		=>	'required|date'
		];
	}

}
