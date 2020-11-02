<?php
namespace Phpfw\Component\Container;

use Phpfw\Component\Container\Exception\BindingResolutionException;
use ReflectionParameter;
use ReflectionMethod;

class DependencyManager
{
	/**
	 * 
	 * Resolve all of the dependencies from the ReflectionParameters.
	 *
	 * @param  array  $parameters
	 * @param  array  $primitives
	 * 
	 * @return array
	 * 
	 */
	public function getDependencies(Container $container,  $parameters,  array $primitives = array())
	{
		$dependencies = array();

		foreach ($parameters as $parameter)
		{
		$dependency = $parameter->getClass();

			// If the class is null, it means the dependency is a string or some other
			// primitive type which we can not resolve since it is not a class and
			// we will just bomb out with an error since we have no-where to go.
			if (array_key_exists($parameter->name, $primitives))
			{
				$dependencies[] = $primitives[$parameter->name];
			}
			elseif (is_null($dependency))
			{
				$dependencies[] = $this->resolveNonClass($parameter);
			}
			else
			{
				$dependencies[] = $this->resolveClass($container,  $parameter);
			}
		}

		return (array) $dependencies;
	}

	/**
	 * Resolve a non-class hinted dependency.
	 *
	 * @param  ReflectionParameter  $parameter
	 * @return mixed
	 *
	 * @throws BindingResolutionException
	 */
	protected function resolveNonClass(ReflectionParameter $parameter)
	{
		if ($parameter->isDefaultValueAvailable())
		{
			return $parameter->getDefaultValue();
		}

		$message = "Unresolvable dependency resolving [$parameter] in class {$parameter->getDeclaringClass()->getName()}";

		throw new BindingResolutionException($message);
	}

	/**
	 * 
	 * Resolve a class based dependency from the container.
	 *
	 * @param  ReflectionParameter  $parameter
	 * @return mixed
	 *
	 * @throws BindingResolutionException
	 * 
	 */
	protected function resolveClass(Container $container,  ReflectionParameter $parameter)
	{
		try {
			return $container->make($parameter->getClass()->name);
		}

		// If we can not resolve the class instance, we will check to see if the value
		// is optional, and if it is we will return the optional parameter value as
		// the value of the dependency, similarly to how we do this with scalars.
		catch (BindingResolutionException $e) {
			if ($parameter->isOptional()) {
				return $parameter->getDefaultValue();
			}

			throw $e;
		}
	}

	/**
	 * 
	 * Resolve method dependency
	 * 
	 * @param \ReflectionClass
	 * @param Object $instance
	 * @param array $dependency
	 * 
	 * @return mixed
	 * 
	 */
	public function resolveMethod(ReflectionMethod $reflector, $instance, array $dependencies)
	{
		return $reflector->invokeArgs($instance, $dependencies);
	}

	/**
	 * 
	 * If extra parameters are passed by numeric ID, rekey them by argument name.
	 *
	 * @param  array  $dependencies
	 * @param  array  $parameters
	 * 
	 * @return array
	 * 
	 */
	public function keyParametersByArgument(array $dependencies,  array $parameters)
	{
		foreach ($parameters as $key => $value) {
			if (is_numeric($key)) {
				unset($parameters[$key]);

				$parameters[$dependencies[$key]->name] = $value;
			}
		}

		return $parameters;
	}
}