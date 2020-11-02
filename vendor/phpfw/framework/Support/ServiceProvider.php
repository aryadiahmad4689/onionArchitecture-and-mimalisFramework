<?php
namespace Phpfw\Component\Support;


abstract class ServiceProvider
{
	/**
	 * 
	 * The Application instance.
	 *
	 * @var \App\Foundation\Application
	 * 
	 */
	protected $app;

	/**
	 * 
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 * 
	 */
	protected $defer = false;


	/**
	 * 
	 * Create a new service provider instance.
	 *
	 * @param  \App\Foundation\Application     $app
	 * 
	 * @return void
	 * 
	 */
	public function __construct($app)
	{
		$this->app = $app;
	}

	/**
	 * 
	 * Bootstrap the application events.
	 *
	 * @return void
	 * 
	 */
	public function boot() {}

	/**
	 * 
	 * Register the service provider.
	 *
	 * @return void
	 * 
	 */
	abstract public function register();


	/**
	 * 
	 * Get the services provided by the provider.
	 *
	 * @return array
	 * 
	 */
	public function provides()
	{
		return array();
	}

	/**
	 * 
	 * Get the events that trigger this service provider to register.
	 *
	 * @return array
	 * 
	 */
	public function when()
	{
		return array();
	}

	/**
	 * 
	 * Determine if the provider is deferred.
	 *
	 * @return bool
	 * 
	 */
	public function isDeferred()
	{
		return $this->defer;
	}
}