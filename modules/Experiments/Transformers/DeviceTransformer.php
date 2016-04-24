<?php

namespace Modules\Experiments\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Experiments\Entities\PhysicalDevice;

class DeviceTransformer extends TransformerAbstract
{
	public function transform(PhysicalDevice $physicalDevice)
	{
		return [
			'id'	=>	$physicalDevice->id,
			'name'	=>	$physicalDevice->device->name,
			'physical_device'	=>	$physicalDevice->name
		];
	}
}