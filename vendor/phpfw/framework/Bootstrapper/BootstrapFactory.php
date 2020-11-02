<?php
namespace Phpfw\Component\Bootstrapper;

use Phpfw\Component\Foundation\Application;

class BootstrapFactory
{
	/**
	 * Bootstrap application
	 * @return \Phpfw\Component\Foundation\Application
	 */
	public function bootstrap()
	{
		$app = new Application;
		
		//Register any application bootstrappers
		//Be careful tinker the following order
		//The arbitrary order will break your app
		$bootstrappers = array(
			new Bootstrap($app), 
			new ConfigRepositoryBootstrap($app), 
			new FacadeBootstrap($app), 
			new ServiceProviderBootstrap($app), 
			new LocalizationBootstrap($app), 
			new HttpKernelBootstrap($app)
		);
		
		foreach ($bootstrappers as $bootstrapper) {
			$bootstrapper->bootstrap();
		}
		
		return $app;
	}
}