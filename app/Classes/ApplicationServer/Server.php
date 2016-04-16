<?php

namespace App\Classes\ApplicationServer;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use GuzzleHttp\Exception\ConnectException;

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

	/**
	 * Is server reachable ?
	 * @var boolean
	 */
	protected $notAvailable;

	public function __construct($ip)
	{
		$this->ip = mb_substr($ip, -1) == "/" ? substr($ip, 0, count($ip) - 2) : $ip;

		$this->client = new Client([
			"base_uri"	=>	"http://" . $this->ip,
			"timeout"	=>	3.0	
		]);

		// $this->notAvailable = false;
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
		$body = $this->get("server/experiments");

		$experiments = isset($body["data"]) ? $body["data"] : [];
		
		foreach ($experiments as $key => $experiment) {
			$experiments[$key]["ip"] = $this->ip;
		}

		return $experiments;
	}

	protected function get($segments)
	{
		$segments = substr($segments,0,1) == "/" ? substr($segments, 1, count($segments) - 1) : $segments;
		$url = $this->apiPrefix . "/" . $segments;
		try {
			$response = $this->client->get($url);
			$response = $this->responseToArray($response);
		} catch(ConnectException $e) {
			$this->notAvailable;
			$response = null;
		}
		return $response;
	}

	protected function responseToArray($response)
	{
		return json_decode($response->getBody(), true);
	}
	
}