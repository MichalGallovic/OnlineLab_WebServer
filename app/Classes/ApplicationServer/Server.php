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
	protected $server;

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

	public function __construct($server)
	{
		$this->server = mb_substr($server, -1) != "/" ? $server . "/" : $server;
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
		return isset($body["data"]) ? $body["data"] : [];
	}

	protected function prepareUrl($segments = null)
	{
		$segments = $segments[0] != "/" ? "/" . $segments : $segments;
		return $this->server . $this->apiPrefix . $segments;
	}

	protected function responseToArray($response)
	{
		return json_decode($response->getBody(), true);
	}
	
}