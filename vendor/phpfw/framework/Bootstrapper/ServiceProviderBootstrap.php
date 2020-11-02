<?php
namespace Phpfw\Component\Bootstrapper;

class ServiceProviderBootstrap extends Bootstrap
{
	/**
	 * @{inheritdoc}
	 * See \Phpfw\Component\Bootstrapper\BootstrapInterface::bootstrap()
	 */
	public function bootstrap()
	{
		$providers = $this->app['config']['application']['providers'];
		$this->app->getProviderRepository()->load($this->app, $providers);
	}
}