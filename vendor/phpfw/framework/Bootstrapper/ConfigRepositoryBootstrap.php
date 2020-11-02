<?php
namespace Phpfw\Component\Bootstrapper;

use Phpfw\Component\Config\Config;
use Phpfw\Component\Config\Repository;

class ConfigRepositoryBootstrap extends Bootstrap
{
	/**
	 * @{inheritdoc}
	 * See \Phpfw\Component\Bootstrapper\BootstrapInterface::bootstrap()
	 */
	public function bootstrap()
	{
		foreach (glob(CONFIG_ROOT_PATH . DS . '*') as $path) {
			$parts = explode(DS, $path);
			$keys = explode('.', end($parts));

			if (is_readable($path)) {
				Config::set($keys[0], require_once $path);
			}
		}
		
		$this->registerConfigFileRepository();
	}

	/**
	 * Register file configuration repository
	 * @return void
	 */	
	private function registerConfigFileRepository()
	{
		$repository = new Repository($this->app->getConfigLoader());
		$this->app->instance('config', $repository);
	}
}