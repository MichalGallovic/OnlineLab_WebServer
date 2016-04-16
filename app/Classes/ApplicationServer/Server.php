<?php

namespace App\Classes\ApplicationServer;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;

/**
*  Olm App server communication wrapper
*  Guzzle heavy use
*/
class Server
{
	/**
	 * Server ip address
	 * @var [type]
	 */
	protected $ip;

	/**
	 * Guzzle HTTP client
	 * @var GuzzleHttp\Client
	 */
	protected $client;

	/**
	 * Olm app server API prefix
	 * @var [type]
	 */
	protected $apiPrefix = "api";

	/**
	 * Experiments available on server
	 * @var array
	 */
	protected $experiments;

	/**
	 * Softwares available on server
	 * @var array
	 */
	protected $softwares;

	/**
	 * Device types available on server
	 * @var array
	 */
	protected $deviceTypes;

	public function __construct($ip)
	{
		$this->ip = mb_substr($ip, -1) != "/" ? $ip . "/" : $ip;
		$this->client = new Client();
	}

	public function deviceTypes()
	{
		$this->deviceTypes = $this->getDeviceTypes();

		return $this->deviceTypes;
	}

	public function softwares()
	{
		$this->softwares = $this->getSoftwares();

		return $this->softwares;
	}

	public function experiments()
	{
		$this->experiments = $this->getExperiments();

		return $this->experiments;
	}

	protected function getDeviceTypes()
	{
		$experiments = $this->getExperimentsCollection();
		return $experiments->unique('device')->values()->lists("device");
	}

	protected function getSoftwares()
	{
		$experiments = $this->getExperimentsCollection();
		return $experiments->unique('software')->values()->lists("software");
	}

	protected function getExperimentsCollection()
	{
		if(!isset($this->experiments)) {
			$this->experiments = $this->getExperiments();
		}

		return new Collection($this->experiments);
	}

	protected function getExperiments()
	{
		$url = $this->prepareUrl("server/experiments");

		$body = $this->responseToArray($this->client->get($url));
		$experiments = isset($body["data"]) ? $body["data"] : [];
		
		foreach ($experiments as $key => $experiment) {
			$experiments[$key]["ip"] = $this->ip;
		}

		return $experiments;
	}

	protected function prepareUrl($segments = null)
	{
		$segments = $segments[0] != "/" ? "/" . $segments : $segments;
		return $this->ip . $this->apiPrefix . $segments;
	}

	protected function responseToArray($response)
	{
		return json_decode($response->getBody(), true);
	}
	
}