<?php
namespace Phpfw\Component\Routing;

use Phpfw\Component\Http\ServerRequest;
use Phpfw\Component\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$config = $this->app['config']['router'];
		$basepath = $config['basepath'];
		$fileName = $config['filename'];
		$this->app['route']->requireRoute($basepath, $fileName);
	}

	public function register()
	{
		$this->registerController();
		$this->registerMiddleware();
		$this->registerRoute();
	}
	
	public function registerRoute()
	{
		$this->app->singleton('route', function($app) {
			return new Route($app, $app['controller'], $app['pipeline.middleware']);
		});
	}
	
	public function registerController()
	{
		$this->app->singleton('controller', 'Phpfw\Component\Routing\Controller\ControllerManager');
	}
	
	public function registerMiddleware()
	{
		$this->app->singleton('pipeline.middleware', 'Phpfw\Component\Routing\PipelineMiddleware');
	}
}
