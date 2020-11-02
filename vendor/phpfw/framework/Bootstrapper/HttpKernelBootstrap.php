<?php
namespace Phpfw\Component\Bootstrapper;

use Phpfw\Component\Support\Facades\Facade;

class HttpKernelBootstrap extends Bootstrap
{
	/**
	 * @{inheritdoc}
	 * See \Phpfw\Component\Bootstrapper\BootstrapInterface::bootstrap()
	 */
	public function bootstrap()
	{
		$this->app->singleton(\Phpfw\App\Http\Kernel::class, function($app) {
			return new \Phpfw\App\Http\Kernel($app);
		});
	}
}