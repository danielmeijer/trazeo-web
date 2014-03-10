<?php

namespace Trazeo\FrontBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TrazeoFrontBundle extends Bundle
{
	public function getParent()
	{
		return 'ApplicationSonataUserBundle';
	}
}
