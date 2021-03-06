<?php namespace Modules\Experiments\Http\Requests;

use Modules\Experiments\Entities\Server;
use Illuminate\Foundation\Http\FormRequest;

class ServerUpdateRequest extends FormRequest {

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
			"name"	=>	"required",
			"ip"	=>	"required|ip|unique:servers,ip," . $this->route()->parameters()["id"],
			"node_port" => "required"
		];
	}

}
