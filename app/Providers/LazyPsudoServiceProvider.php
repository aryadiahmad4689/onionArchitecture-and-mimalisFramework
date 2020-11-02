<?php
namespace Phpfw\App\Providers;

class LazyPsudoServiceProvider extends AppServiceProvider
{
	protected $defer = true;
	
	public function register()
	{
		$this->app->bind('lazy.psudo', function($app) {
			return (new PsudoClass($app))->argumentInstanceOf();
		});
	}
	
	public function when()
	{
		return array('ProviderHook');
	}
	
	public function provides()
	{
		return array('lazy.psudo');
	}
}