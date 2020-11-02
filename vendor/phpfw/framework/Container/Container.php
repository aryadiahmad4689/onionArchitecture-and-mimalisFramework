<?php
namespace Phpfw\Component\Container;

use Closure;
use ArrayAccess;
use ReflectionClass;
use Phpfw\Component\Container\Exception\BindingResolutionException;
use Phpfw\Component\Contract\Container\ContainerInterface as IContainer;

class Container implements IContainer, ArrayAccess
{
	/**
	 * {@inheritdoc}
	 */
	protected $bindings = array();

	/**
	 * {@inheritdoc}
	 */
	protected $instances = array();

	/**
	 * {@inheritdoc}
	 */
	protected $resolved = array();

	/**
	 * {@inheritdoc}
	 */
	public function dropStaleInstance($abstract)
	{
		unset($this->instances[$abstract]);
	}

	/**
	 * {@inheritdoc}
	 */
    public function isSingleton($abstract)
    {
        if(isset($this->bindings[$abstract]['singleton'])) {
            $singleton = $this->bindings[$abstract]['singleton'];
        } else {
            $singleton = false;
        }

        return isset($this->instances[$abstract]) || $singleton === true;
    }

	/**
	 * {@inheritdoc}
	 */
    public function isBuildable($concrete,  $abstract)
    {
        return $concrete === $abstract || $concrete instanceof Closure;
    }

	/**
	 * {@inheritdoc}
	 */
    public function resolved($abstract)
    {
        return isset($this->resolved[$abstract]) || isset($this->instances[$abstract]);
    }

	/**
	 * {@inheritdoc}
	 */
    public function bound($abstract)
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

	/**
	 * {@inheritdoc}
	 */
    public function getConcrete($abstract)
    {
    	if(!isset($this->bindings[$abstract])) {
    		return $abstract;
    	}
        return $this->bindings[$abstract]['concrete'];
    }

	/**
	 * {@inheritdoc}
	 */	
    public function registerDependencyManager()
    {
        return new DependencyManager();
    }

	/**
	 * {@inheritdoc}
	 */
    public function build($concrete,  $parameters = array())
    {
        if ($concrete instanceof Closure)
        {
            return $concrete($this, $parameters);
        }

        $reflector = new ReflectionClass($concrete);

        if (! $reflector->isInstantiable())
        {
            $message = "Target [$concrete] is not instantiable.";

            throw new BindingResolutionException($message);
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor))
        {
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();
		$manager = $this->registerDependencyManager();
		$manager->keyParametersByArgument(
            $dependencies, $parameters
        );

        $instances = $manager->getDependencies(
            $this,  $dependencies, $parameters
        );

        return $reflector->newInstanceArgs($instances);
    }

	/**
	 * {@inheritdoc}
	 */
    public function make($abstract, $parameters = array())
    {
        if (isset($this->instances[$abstract]))
        {
            return $this->instances[$abstract];
        }

        $concrete = $this->getConcrete($abstract);

        if ($this->isBuildable($concrete, $abstract))
        {
            $object = $this->build($concrete, $parameters);
        }
        else
        {
            $object = $this->make($concrete, $parameters);
        }

        if ($this->isSingleton($abstract))
        {
            $this->instances[$abstract] = $object;
        }

        $this->resolved[$abstract] = true;

        return $object;
    }

	/**
	 * {@inheritdoc}
	 */
    public function instance($abstract,  $instance)
    {
        $bound = $this->bound($abstract);

        $this->instances[$abstract] = $instance;

        if ($bound)
        {
            $this->make($abstract);
        }
    }

	/**
	 * {@inheritdoc}
	 */
    public function bind($abstract,  $concrete,  $singleton = false)
    {
        $this->dropStaleInstance($abstract);

        $concrete = (is_null($concrete))?$abstract:$concrete;
		
        $this->bindings[$abstract] = compact("concrete",  "singleton");
	}

	/**
	 * {@inheritdoc}
	 */
    public function singleton($abstract,  $concrete = null)
    {
        $this->bind($abstract,  $concrete,  true);
    }

	/**
	 * {@inheritdoc}
	 */
    public function bindIf($abstract,  $concrete, $singleton = false)
    {
        if(!$this->bound($abstract)) {
            $this->bind($abstract,  $concrete,  $singleton);
        }
    }

	/**
	 * {@inheritdoc}
	 */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * 
     * Determine if a given offset exists
     * 
     * @param string $key
     * 
     * @return boolean
     * 
     */
    public function offsetExists($key)
    {
        return isset($this->bindings[$key]);
    }

    /**
     * 
     * Get a value at a given offset
     * 
     * @param string $key
     * 
     * @return mixed
     * 
     */
    public function offsetGet($key)
    {
        return $this->make($key);
    }

    /**
     * 
     * Set a value at a given offset
     * 
     * @param string $key
     * @param mixed #value
     * 
     * @return void
     * 
     */
    public function offsetSet($key,  $value)
    {
        if(!$value instanceof Closure) {
            $value = function () use ($value) {
                return $value;
            };
        }

        $this->bind($key,  $value);
    }

    /**
     * 
     * Unset the value of a given offset
     * 
     * @param string $key
     * 
     * @return boolean
     * 
     */
    public function offsetUnset($key)
    {
        unset($this->bindings[$key],  $this->instances[$key],  $this->resolved[$key]);
    }

    /**
     * 
     * Dynamically access container services
     * 
     * @param string $key
     * 
     * @return mixed
     * 
     */
    public function __get($key)
    {
        return $this[$key];
    }

    /**
     * 
     * Dynamically set container services
     * 
     * @param string $key
     * @param mixed $value
     * 
     * @return void
     * 
     */
    public function __set($key,  $value)
    {
        $this[$key] = $value;
    }
}