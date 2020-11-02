<?php

namespace Phpfw\App\Http\Middlewares;

use Closure;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Phpfw\Component\Http\Response;
use Phpfw\Component\Contract\Http\Middleware\MiddlewareInterface as IMiddleware;

class AuthMiddleware implements IMiddleware
{
	public function handle(RequestInterface $request, Closure $next): ResponseInterface
	{
		if(!isset($_SESSION[$request->getServerParams()['SCRIPT_NAME']])) {
			echo "Sorry, You have not permission to access this page!";
			return new Response();
		}
		return $next($request);
	}
}