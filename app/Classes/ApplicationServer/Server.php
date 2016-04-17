<?php

namespace App\Classes\ApplicationServer;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use GuzzleHttp\Exception\ClientException;
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
	protected $available;

	/**
	 * Is redis available
	 * @var boolean
	 */
	protected $redisAvailable;

	/**
	 * Is queue available
	 * @var boolean
	 */
	protected $queueAvailable;

	/**
	 * Is database available
	 * @var [type]
	 */
	protected $databaseAvailable;

	/**
	 * Is server reachable
	 * @var boolean
	 */
	protected $reachable;

	/**
	 * Last response code
	 * @var [type]
	 */
	protected $lastResponseCode;

	public function __construct($ip)
	{
		$this->ip = mb_substr($ip, -1) == "/" ? substr($ip, 0, count($ip) - 2) : $ip;

		$this->client = new Client([
			"base_uri"	=>	"http://" . $this->ip,
			"timeout"	=>	2.0	
		]);

		$this->check();
	}

	 /**
     * Gets the Server ip address.
     *
     * @return [type]
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Gets the Is server reachable ?.
     *
     * @return boolean
     */
    public function getAvailability()
    {
        return $this->available;
    }

    public function check()
    {
    	$response = $this->get("server/status");

    	$this->databaseAvailable = isset($response["database"]) ? $response["database"] : false;
    	$this->queueAvailable = isset($response["queue"]) ? $response["queue"] : false;
    	$this->redisAvailable = isset($response["redis"]) ? $response["redis"] : false;
    	$this->available = $this->serverAvailable();
    }

    protected function serverAvailable()
    {
    	return ($this->lastResponseCode / 100) != 5 && 
    	$this->databaseAvailable && 
    	$this->queueAvailable &&
    	$this->redisAvailable &&
    	$this->reachable;
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

	protected function get($segments = null, $force = false)
	{
		if(!is_null($this->reachable) && !$force) {
			if(!$this->reachable || !$this->databaseAvailable) return [];
		}

		$segments = substr($segments,0,1) == "/" ? substr($segments, 1, count($segments) - 1) : $segments;
		$url = $this->apiPrefix . "/" . $segments;
		$response = null;
		try {
			$response = $this->client->get($url);
			$this->lastResponseCode = $response->getStatusCode();
			$this->reachable = true;
			$response = $this->responseToArray($response);
		} catch(ConnectException $e) {
			$this->reachable = false;
		} catch(ClientException $e) {
			$this->lastResponseCode = $e->getResponse()->getStatusCode();
		}

		return $response;
	}

	protected function responseToArray($response)
	{
		return json_decode($response->getBody(), true);
	}

    /**
     * Gets the Is server reachable.
     *
     * @return boolean
     */
    public function getReachable()
    {
        return $this->reachable;
    }

    /**
     * Gets the Is database available.
     *
     * @return [type]
     */
    public function getDatabaseAvailable()
    {
        return $this->databaseAvailable;
    }

    /**
     * Gets the Is queue available.
     *
     * @return boolean
     */
    public function getQueueAvailable()
    {
        return $this->queueAvailable;
    }

    /**
     * Gets the Is redis available.
     *
     * @return boolean
     */
    public function getRedisAvailable()
    {
        return $this->redisAvailable;
    }
}