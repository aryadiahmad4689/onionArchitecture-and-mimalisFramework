<?php
namespace Phpfw\Component\Foundation;

use Closure;
use Phpfw\Component\Container\Container as BaseContainer;

class Container extends BaseContainer
{
    /**
     * 
     * The names of the loaded deferred service providers.
     *
     * @var array
     * 
     */
    protected $loadedDeferredProviders = array();

    /**
     * 
     * The deferred services and their providers.
     *
     * @var array
     * 
     */
    protected $deferredServices = array();

    /**
     * 
     * Load and boot all of the remaining deferred providers.
     *
     * @return void
     * 
     */
    public function loadDeferredProviders()
    {
        foreach ($this->deferredServices as $service => $provider) {
            $this->loadDeferredProvider($service);
        }

        $this->deferredServices = array();
    }

    /**
     * 
     * Load the provider for a deferred service.
     *
     * @param  string  $service
     * 
     * @return void
     * 
     */
    protected function loadDeferredProvider($service)
    {
        $provider = $this->deferredServices[$service];

        if (! isset($this->loadedProviders[$provider])) {
            $this->registerDeferredProvider($provider, $service);
        }
    }

    /**
     * 
     * Register a deferred provider and service.
     *
     * @param  string  $provider
     * @param  string  $service
     * 
     * @return void
     * 
     */
    public function registerDeferredProvider($provider, $service = null)
    {
        if ($service) {
			unset($this->deferredServices[$service]);
		}

        $this->register($instance = new $provider($this));
        
		$instance->boot();
    }

    /**
     * 
     * Resolve the given type from the container.
     *
     * (Overriding Container::make)
     *
     * @param  string  $abstract
     * @param  array   $parameters
     * 
     * @return mixed
     * 
     */
    public function make($abstract, $parameters = array())
    {
        if (isset($this->deferredServices[$abstract])) {
            $this->loadDeferredProvider($abstract);
        }

        return parent::make($abstract, $parameters);
    }

    /**
     * 
     * Determine if the given abstract type has been bound.
     *
     * (Overriding Container::bound)
     *
     * @param  string  $abstract
     * 
     * @return bool
     * 
     */
    public function bound($abstract)
    {
        return isset($this->deferredServices[$abstract]) || parent::bound($abstract);
    }

    /**
     * Set the application's deferred services.
     *
     * @param  array  $services
     * @return void
     */
    public function setDeferredServices(array $services)
    {
        $this->deferredServices = $services;
    }

    /**
     * Determine if the given service is a deferred service.
     *
     * @param  string  $service
     * @return bool
     */
    public function isDeferredService($service)
    {
        return isset($this->deferredServices[$service]);
    }
}