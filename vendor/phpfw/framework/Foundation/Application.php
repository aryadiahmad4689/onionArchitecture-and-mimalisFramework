<?php
namespace Phpfw\Component\Foundation;

use Phpfw\Component\Config\FileLoader;
use Psr\Http\Message\RequestInterface;
use Phpfw\Component\Event\EventServiceProvider;
use Phpfw\Component\Http\HttpServiceProvider;
use Phpfw\Component\Routing\RouteServiceProvider;
use Phpfw\Component\Contract\Container\ContainerInterface;

class Application extends Container
{
	const VERSION = '1.0';
	
	protected $serviceProviders = array();
	
	protected $loadedProviders = array();
	
	public function __construct()
	{
		$this->registerBaseBindings();
		$this->registerBaseServiceProviders();
	}
	
	public function registerBaseBindings()
	{
		$this->instance(ContainerInterface::class,  $this);
	}
	
	public function register($provider)
	{
		if ($registered = $this->getRegistered($provider)) {
			return $registered;
		}

		if (is_string($provider)) {
			$provider = $this->resolveProviderClass($provider);
		}
		//register current provider
		$provider->register();

		//mark current provider as registered
		$this->markAsRegistered($provider);

		$provider->boot();
		
		return $provider;
	}
	
	public function markAsRegistered($provider)
	{
		$this->loadedProviders[] = $provider;
		$this->serviceProviders[] = $provider;
	}
	
	public function getRegistered($provider)
	{
		$name = is_string($provider) ? $provider : get_class($provider);

		if (array_key_exists($name, $this->loadedProviders)) {
			return $this->serviceProviders[$name];
		}
	}
	
	public function resolveProviderClass($provider)
	{
		return new $provider($this);
	}
	
	public function registerBaseServiceProviders()
	{
		foreach(array('Event', 'Http') as $name) {
			$this->{"register{$name}Provider"}();
		}
	}
	
	public function registerHttpProvider()
	{
		$this->register(new HttpServiceProvider($this));
	}

	public function registerEventProvider()
	{
		$this->register(new EventServiceProvider($this));
	}
	
	public function registerRouteProvider()
	{
		$this->register(new RouteServiceProvider($this));
	}
	
	public function handle()
	{
		try {
			$this->registerRouteProvider();
			$this['route']->dispatch($this[RequestInterface::class]);
		} catch (\Exception $e) {
			throw new \Exception($e);
		}
	}
	
    public function getProviderRepository()
    {
        $manifest = $this['config']['application.manifest'];

        return new ProviderRepository($manifest);
    }
	
	public function getConfigLoader()
	{
		return new FileLoader();
	}
}