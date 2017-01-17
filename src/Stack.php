<?php

namespace Idealo\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Stack implements DelegateInterface
{
    /**
     * @var MiddlewareInterface[]
     */
    protected $middlewares = [];

    /**
     * @var ResponseInterface
     */
    protected $defaultResponse;

    public function __construct(ResponseInterface $response, MiddlewareInterface ...$middlewares)
    {
        $this->defaultResponse = $response;
        $this->middlewares = $middlewares;
    }

    private function withoutMiddleware(MiddlewareInterface $middleware): DelegateInterface
    {
        return new self(
            $this->defaultResponse,
            ...array_filter(
                $this->middlewares,
                function ($m) use ($middleware) {
                    return $middleware !== $m;
                }
            )
        );
    }

    public function process(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->middlewares[0] ?? false;

        return $middleware
            ? $middleware->process(
                $request,
                $this->withoutMiddleware($middleware)
            )
            : $this->defaultResponse;
    }
}
