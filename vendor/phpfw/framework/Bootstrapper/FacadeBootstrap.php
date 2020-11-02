<?php
namespace Phpfw\Component\Bootstrapper;

use Phpfw\Component\Support\Facades\Facade;

class FacadeBootstrap extends Bootstrap
{
	/**
	 * @{inheritdoc}
	 * See \Phpfw\Component\Bootstrapper\BootstrapInterface::bootstrap()
	 */
	public function bootstrap()
	{
		Facade::clearResolvedInstances();
		Facade::setFacadeApplication($this->app);
	}
}