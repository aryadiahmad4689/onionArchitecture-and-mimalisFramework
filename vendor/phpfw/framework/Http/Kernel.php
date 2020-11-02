<?php
namespace Phpfw\Component\Http;

use Phpfw\Component\Http\Response;
use Phpfw\Component\Pipeline\Pipeline;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Phpfw\Component\Contract\Container\ContainerInterface;
use Phpfw\Component\Contract\Http\Middleware\MiddlewareInterface;

class Kernel
{
	/** The default handler method executed by pipeline **/
	const HANDLER_METHOD = 'handle';

	/**
	 * Container instance
	 * @var \Repository\Component\Contracts\Container\ContainerInterface $app
	 */
	protected $app;

	/**
	 * Global middlewares
	 * @var array $middlewares
	 */
	protected $middlewares = array();
	
	/**
	 * Enabled middlewares
	 * @var array $enabledMiddlewares
	 */
	protected $enabledMiddlewares = array();

	/**
	 * Disabled middlewares
	 * @var array $disabledMiddlewares
	 */
	protected $disabledMiddlewares = array();
	
	/**
	 * Disabled all middleware identifier
	 * @var bool $disabledMiddleware
	 */
	protected $disabledMiddleware = false;

	/**
	 * @param \Repository\Component\Contracts\Container\ContainerInterface $app
	 */
	public function __construct(ContainerInterface $app)
	{
		$this->app = $app;
	}
	
	/**
	 * Disabled all middleware
	 * 
	 * @return void
	 */	
	public function disabledAllMiddleware()
	{
		$this->disabledMiddleware = true;
	}

	/**
	 * Determine if the middleware is disabled
	 * 
	 * @return bool true When disabled, false otherwise
	 */	
	public function isMiddlewareDisabled()
	{
		if ($this->disabledMiddleware)
			return true;
		
		return false;
	}

	/**
	 * Add new middleware to the middleware list
	 * 
	 * @param string|array|object $middleware
	 * 
	 * @return void
	 */	
	public function addMiddleware($middleware)
	{
		if (!is_array($middleware))
			$middleware = [$middleware];
		
		$middleware = array_merge($this->middlewares, $middleware);
		
		$this->middlewares = $middleware;
	}

	/**
	 * Add middleware only to the the enabled middleware list
	 * 
	 * @param array $middleware
	 * 
	 * @return void
	 */	
	public function addOnlyEnableMiddleware(array $middlewares = array())
	{
		$this->enabledMiddlewares = $middlewares;
	}

	/**
	 * Add middleware only to the the disabled middleware list
	 * 
	 * @param array $middleware
	 * 
	 * @return void
	 */	
	public function addOnlyDisabledMiddleware(array $middlewares = array())
	{
		$this->disabledMiddlewares = $middlewares;
	}

	/**
	 * Handle inncoming request to the application
	 * 
	 * @param \Psr\Http\Message\RequestIntterface $request
	 * 
	 * @return \Psr\Http\Message\ResponseInterface
	 */	
	public function handle(RequestInterface $request): ResponseInterface
	{
		$middlewares = $this->createMiddlewareStages($this->getMiddlewares());

		$process = (new Pipeline())
			->send($request)
			->through($middlewares, self::HANDLER_METHOD)
			->then(function () {
				$this->app->handle();
			})
			->execute();
		
		return $process ?? new Response;
	}

	/**
	 * Create pipeline stage by the given middlewares list
	 * 
	 * @param array $middleware
	 * 
	 * @return array
	 */	
	public function createMiddlewareStages(array $middlewares)
	{
		$stages = array();
		$abstract = MiddlewareInterface::class;
		$ex = "Stage middleware should be instanceof [$abstract]";

		foreach ($middlewares as $middleware) {
			if (is_string($middleware)) {
				$middleware = $this->app->make($middleware);

				if (!$middleware instanceof MiddlewareInterface) {
					throw new \Exception($ex);
				}
				
				$stages[] = $middleware;
			} else {
				if (!$middleware instanceof MiddlewareInterface) {
					throw new \Exception($ex);
				}

				$stages[] = $middleware;
			}
		}
		
		return $stages;
	}

	/**
	 * Get global middlewares and enabled middlewares
	 * 
	 * @return array
	 */	
	public function getMiddlewares()
	{
		if ($this->disabledMiddleware) return [];
			
		if (count($this->enabledMiddlewares) > 0)
			return $this->enabledMiddlewares;

		if (count($this->disabledMiddlewares) > 0) {
			$enabledMiddlewares = array();
			
			foreach($this->middlewares as $middleware) {
				if (is_string($middleware)) {
					$middlewareClass = $middleware;
				} else {
					$middlewareClass = get_class($middleware);
				}
				
				if (!in_array($middlewareClass, $this->disabledMiddlewares)) {
					$enabledMiddlewares[] = $middleware;
				}
			}
			
			return $enabledMiddlewares;
		}

		return $this->middlewares;
	}
}