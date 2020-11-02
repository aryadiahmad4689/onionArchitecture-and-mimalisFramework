<?php
namespace Phpfw\Component\Bootstrapper;

class LocalizationBootstrap extends Bootstrap
{
	/**
	 * @{inheritdoc}
	 * See \Phpfw\Component\Bootstrapper\BootstrapInterface::bootstrap()
	 */
	public function bootstrap()
	{
		date_default_timezone_set($this->app['config']['application']['timezone']);
	}
}