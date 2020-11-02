<?php
namespace Phpfw\App\Http;

use Phpfw\Component\Http\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
	protected $middlewares = array();

	/**
	 * Define your enabled middlewares list here
	 * @var array $enabledMiddlewares
	 */
	protected $enabledMiddlewares = array(
		//\Phpfw\App\Http\Middlewares\AuthMiddleware::class
	);

	/**
	 * Define your disabled middlewares list here
	 * @var array $disabledMiddlewares
	 */
	protected $disabledMiddlewares = array();

	/**
	 * If you switch to false, It's means you disabled any middlewares
	 * @var bool $disabledMiddleware
	 */
	protected $disabledMiddleware = false;
}