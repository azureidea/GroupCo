<?php

namespace src\Service\NodeCenter\Service\Impl;

use src\Service\NodeCenter\Service\Rely\NodeCenterBaseService;
use src\Service\NodeCenter\Service\NodeCenterService;
use Cache;

class NodeCenterServiceImpl extends NodeCenterBaseService implements NodeCenterService
{
	public function getService($serviceName)
	{	
		$actvieServices = [];
		$nodes = Cache::get('nodes');
        if (!$nodes) {
            $nodes = $this->getNodeCenterDao()->getActiveNodes();
            Cache::set('nodes', $nodes, 3600);
        }

		foreach ($nodes as $node) {
			$services = $node['services'];
			$services = explode(",", $services);
			foreach ($services as $service) {
				$actvieServices[$service][] = ['ip' => $node['ip'], 'port' => $node['port']];
			}
		}

		if (!isset($actvieServices[$serviceName])) return [];

		//模拟随机算法
		$count = count($actvieServices[$serviceName]);
		if ($count >= 1) {
			mt_rand();
			$res = mt_rand(0, $count - 1);
			return $actvieServices[$serviceName][$res];
		}

		return [];
	}

	public function updateService()
	{
		$nodes = $this->getNodeCenterDao()->getActiveNodes();
        return Cache::set('nodes', $nodes, 3600);
	}
}