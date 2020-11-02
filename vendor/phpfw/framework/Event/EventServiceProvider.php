<?php
namespace Phpfw\Component\Event;

use Phpfw\Component\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->singleton('event', function($app) {
			return new Dispatcher($app);
		});
	}
}