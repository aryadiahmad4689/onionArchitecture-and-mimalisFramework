<?php
namespace Phpfw\Component\Routing\Controller;

use RuntimeException;
use ReflectionMethod;
use Phpfw\Component\Contract\Container\ContainerInterface;
use Phpfw\Component\Routing\Controller\BaseController;

class ControllerManager
{
	public function __construct()
	{
		//
	}
	
	public function registerApplication(ContainerInterface $app)
	{
		$this->app = $app;
	}
	
	public function resolveControllerClass($abstract, $concrete)
	{
		$this->app->bindIf($abstract, $concrete);
	}
	
	public function resolveControllerMethod(ReflectionMethod $reflector, $abstract, $primitives = array())
	{
		$dependency = $this->app->registerDependencyManager();
		$parameters = $reflector->getParameters();
		
		//Register automatic dependency resolution
		$dependency = $this->app->registerDependencyManager();
		//Get paremeter from request dependency
		$parameters = $reflector->getParameters();
		
		//Resolve dependencies
		switch ($primitives) {
			//If primitives parameter exist
			case count($primitives) > 0:
				$params = array();
				$index = 0;
				
				//Rekey parameters dependency
				//by the given primitives parameter
				foreach ($parameters as $param) {

					$class = $param->getClass();

					if (is_object($class)) {
						$params[$param->name] = $this->app[$class->name];
					} else {
						if (!isset($primitives[$index])) {
							if ($param->isOptional() || $param->isDefaultValueAvailable()) {
								$primitive = $param->getDefaultValue();
							} else {
								throw new \Exception("Method parameter unresolvable.");
							}
						} else {
							$primitive = $primitives[$index];
						}

	 					$params[$param->name] = $primitive;
	 					$index++;
					}
				}

				//Build dependency
				$dependencies = $dependency->getDependencies(
					$this->app, 
					$parameters, 
					$params
				); break;
			default:
				$dependencies = $dependency->getDependencies($this
					->app, $reflector
					->getParameters()
				);
		}

		$controller = $this->app[$abstract];
		if($controller instanceof BaseController) {
			$controller->registerApplication($this->app);

			$dependency->resolveMethod(
				$reflector, 
				$controller, 
				$dependencies
			);
			
			return;
		}
		
		$ex = "Controller must be instance of ";
		$ex.= "[".BaseController::class."]";
		$ex.= "Another Given.";
		throw new RuntimeException($ex);
	}
}