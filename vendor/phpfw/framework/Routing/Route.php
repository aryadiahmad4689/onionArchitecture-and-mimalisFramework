<?php
namespace Phpfw\Component\Routing;

use Closure;
use ReflectionMethod;
use Psr\Http\Message\RequestInterface;
use Phpfw\Component\Contract\Container\ContainerInterface;
use Phpfw\Component\Routing\Controller\ControllerManager;

class Route
{
	private $app;
	
	private $middlewares = array();
	
	private $routes = array();
	
	private $manager;
	
	private $notFound;
	
	public function __construct(ContainerInterface $app, ControllerManager $manager, PipelineMiddleware $pipeline)
	{
		$this->app = $app;
		$this->manager = $manager;
		$this->pipeline = $pipeline;
	}
	
	public function any($pathName, $callback)
	{
		$this->match('GET|POST|PUT|DELETE|OPTIONS|PATCH|HEAD', $pathName, $callback);
		return $this;
	}
	
	public function get($pathName, $callback)
	{
		$this->match('GET', $pathName, $callback);
		return $this;
	}
	
	public function post($pathName, $callback)
	{
		$this->match('POST', $pathName, $callback);
		return $this;
	}
	
	public function middleware($verb, $pathName, $callback)
	{
		$pattern = '/' . trim($pathName, '/');

		foreach (explode('|', $verb) as $method) {
			$this->middlewares[$method][] = array(
				'pattern' => $pattern,
				'fn' => $callback
			);
		}
	}
	
	public function match($verb, $pathName, $callback)
	{
		$pattern = '/' . trim($pathName, '/');

		foreach (explode('|', $verb) as $method) {
			$this->routes[$method][] = array(
				'pattern' => $pattern,
				'fn' => $callback
			);
		}
	}

	public function dispatch(RequestInterface $request)
	{
		$nullPassed = function() {
			$middleware = array();
			$middleware[] = function($input, $next) {
				return $next($input);
			};
			
			return $middleware;
		};
		
		$requestedMethod = ucwords($request->getServerParams()['REQUEST_METHOD']);

		if (isset($this->middlewares[$requestedMethod])) {
			$middlewares = $this->middlewares[$requestedMethod];
			$middlewares = $this->handleRequest($request, $middlewares);
			
			list($numHandled, $middleware, $params) = $middlewares;
			
			if (is_null($middleware)) $middleware = $nullPassed();
			
			(is_array($middleware))?
				$middleware = $middleware:
				$middleware = array($middleware);
				
		} else {
			$middleware = $nullPassed();
		}

		$controllerCallable = function(RequestInterface $request) use ($requestedMethod) {
    		$numHandled = 0;
    		$result = null;
			$handle = $this->handleRequest($request, $this->routes[$requestedMethod]);

			list($numHandled, $callback, $params) = $handle;

    		// If no route was handled, trigger the 404 (if any)
    		if ($numHandled == 0) {
    			if ($this->notFound instanceof Closure) {
    				call_user_func($this->notFound);
				}
				
				header('HTTP/1.1 404 Not Found');
    		}

			$resolved = $this->resolveCallback($numHandled, $callback, $params);
			
			list($numHandled, $response) = $resolved;

    		return $response;
		};

		return $this->pipeline->send($request, $middleware, $controllerCallable);
	}

	public function set404($fn)
	{
		$this->notFound = $fn;
	}
	
	private function handleRequest(RequestInterface $request, $routes)
	{
		// Counter to keep track of the number of routes we've handled
		$numHandled = 0;

		// The current page URL
		$uri = $this->getCurrentUri($request);

		// Variables in the URL
		$urlvars = array();

		// Loop all routes
		foreach ($routes as $route) {

			// we have a match!
			if (preg_match_all('#^' . $route['pattern'] . '$#', $uri, $matches, PREG_SET_ORDER)) {

				// Extract the matched URL parameters (and only the parameters)
				$params = array_map(function($match) {
					$var = explode('/', trim($match, '/'));
					return isset($var[0]) ? $var[0] : null;
				}, array_slice($matches[0], 1));

				$numHandled++;
				return array($numHandled, $route['fn'], $params);

			}

		}
	}
	
	private function resolveCallback($numHandled, $callback, $primitives)
	{
		$numHandled = $numHandled;
		if($callback instanceof Closure) {
			// call the handling function with the URL parameters
			$response = call_user_func_array($callback, $primitives);
		} else {
			if(!is_null($callback))
				$response = $this->resolveMethod($callback, $primitives);
			$response = null;
		}
		
		return array($numHandled, $response);
	}
	
	private function resolveMethod($callback, $primitives)
	{
		$callback = str_parse_callback($callback, 'index');
		$this->manager->registerApplication($this->app);
		$reflector = new ReflectionMethod($callback[0], $callback[1]);
		$this->manager->resolveControllerClass($callback[0], $callback[0]);
		$response = $this->manager->resolveControllerMethod($reflector, $callback[0], $primitives);
	}

	public function getCurrentUri(RequestInterface $request)
	{
		// Current Request URI
		$uri = $request->getServerParams()['REQUEST_URI'];

		// Don't take query params into account on the URL
		if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));

		// Remove trailing slash + enforce a slash at the start
		$uri = '/' . trim($uri, '/');

		return $uri;
	}
	
	public function requireRoute($folder, $filename)
	{
		$folder = trim($folder, '/');
		$filename = trim($filename, '/');
		
		require_once $folder . DS . $filename;
	}
}
