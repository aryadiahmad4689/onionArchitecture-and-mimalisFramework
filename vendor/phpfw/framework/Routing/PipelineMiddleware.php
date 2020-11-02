<?php
namespace Phpfw\Component\Routing;

use Psr\Http\Message\RequestInterface;
use Phpfw\Component\Http\Request;
use Phpfw\Component\Http\Response;
use Phpfw\Component\Pipeline\Pipeline;
use Phpfw\Component\Routing\RouteException;
use Phpfw\Component\Pipeline\PipelineException;

class PipelineMiddleware
{
    public function send(RequestInterface $request, array $middleware, callable $controller) : Response
    {
        try {
            $response = (new Pipeline)
                ->send($request)
                ->through($middleware, 'handle')
                ->then($controller)
                ->execute();

            return $response ?? new Response();
        } catch (PipelineException $ex) {
            throw new RouteException('Failed to send request through middleware pipeline', 0, $ex);
        }
    }
}
