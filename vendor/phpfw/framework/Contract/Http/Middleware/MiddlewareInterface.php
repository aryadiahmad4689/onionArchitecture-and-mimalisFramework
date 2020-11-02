<?php
namespace Phpfw\Component\Contract\Http\Middleware;

use Closure;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface MiddlewareInterface
{
	public function handle(RequestInterface $request, Closure $next): ResponseInterface;
}