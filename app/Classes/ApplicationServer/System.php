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


	public function __construct(array $ips)
	{
		$this->servers = $this->initServers($ips);
		$this->ips = $ips;
	}

	public function experiments()
	{
		return $this->getExperiments();
	}

	
	protected function getExperiments()
	{
		$experiments = new Collection();

		foreach ($this->servers as $server) {
			$experiments = $experiments->merge($server->experiments());
		}

		return $experiments;
	}

	protected function initServers(array $ips)
	{
		$servers = [];

		foreach ($ips as $ip) {
			$servers []= new Server($ip);
		}

		return $servers;
	}

	
}