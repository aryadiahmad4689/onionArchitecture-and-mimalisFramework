<?php
namespace Phpfw\Component\Http;

use Psr\Http\Message\RequestInterface;
use Phpfw\Component\Support\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
{
	public function boot()
	{
		//
	}

	public function register()
	{
		$this->registerHttpRequest();
	}
	
	public function registerHttpRequest()
	{
		$this->app->singleton(RequestInterface::class, function($app) {
			return new ServerRequest();
		});
	}
}
