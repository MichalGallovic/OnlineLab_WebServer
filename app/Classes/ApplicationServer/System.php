<?php

namespace App\Classes\ApplicationServer;

use Illuminate\Support\Collection;
use App\Classes\ApplicationServer\Server;

/**
* Whole System of application servers
*/
class System
{
	/**
	 * IP addresses of the servers
	 * @var array
	 */
	protected $ips;

	/**
	 * Servers
	 * @var array App\Classes\ApplicationServer\Server
	 */
	protected $servers;


	public function __construct(array $ips = [])
	{
		$this->servers = $this->initServers($ips);
		$this->ips = $ips;
	}

	public function physicalExperiments()
	{
		return $this->getExperiments();
	}

	public function devices()
	{
		return $this->getDevices();
	}

	public function experiments()
	{
		$experiments = $this->physicalExperiments()->unique(function($item) {
			return $item["device"].$item["software"];
		});

		$unique = new Collection();

		foreach ($experiments as $experiment) {
			unset($experiment["ip"]);
			unset($experiment["id"]);
			unset($experiment["device_name"]);
			$unique->push($experiment);
		}

		return $unique;
	}

	public function check()
	{
		foreach ($this->servers as $server) {
			$server->check();
		}
	}

	protected function getExperiments()
	{
		$experiments = new Collection();

		foreach ($this->servers as $server) {
			$experiments = $experiments->merge($server->experiments());
		}

		return $experiments;
	}

	protected function getDevices()
	{
		$devices = new Collection();

		foreach($this->servers as $server) {
			$devices = $devices->merge($server->devices());
		}

		return $devices;
	}

	protected function initServers(array $ips)
	{
		$servers = [];

		foreach ($ips as $ip) {
			$servers []= new Server($ip);
		}

		return $servers;
	}

	

    /**
     * Gets the Servers.
     *
     * @return array App\Classes\ApplicationServer\Server
     */
    public function getServers()
    {
        return $this->servers;
    }
}