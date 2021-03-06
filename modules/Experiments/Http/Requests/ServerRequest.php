<?php namespace Modules\Experiments\Http\Requests;

use Modules\Experiments\Entities\Server;
use Illuminate\Foundation\Http\FormRequest;

class ServerRequest extends FormRequest {

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
		$server = Server::withTrashed()->where('ip',$this->input('ip'))->first();

		$excludeId = !is_null($server) ? $server->id : null;

		return [
			"name"	=>	"required",
			"ip"	=>	"required|ip|unique:servers,ip," . $excludeId,
			"node_port"	=>	"required"
		];
	}

}
