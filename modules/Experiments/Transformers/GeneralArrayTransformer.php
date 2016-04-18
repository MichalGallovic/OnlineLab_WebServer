<?php

namespace Modules\Experiments\Transformers;

use League\Fractal\TransformerAbstract;

class GeneralArrayTransformer extends TransformerAbstract
{
	public function transform(array $data)
	{
		return $data;
	}
}