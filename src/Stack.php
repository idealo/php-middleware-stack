<?php

namespace Idealo\Middleware;

use Psr\Http\Middleware\StackInterface;
use Psr\Http\Middleware\ServerMiddlewareInterface;
use Psr\Http\Middleware\MiddlewareInterface;
use Psr\Http\Middleware\DelegateInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

class Stack implements StackInterface
{
    protected $middlewares = [];
    protected $defaultResponse = null;

    public function __construct(ResponseInterface $response, ServerMiddlewareInterface ...$middlewares)
    {
        $this->defaultResponse = $response;
        $this->middlewares = $middlewares;
    }

    public function withMiddleware(MiddlewareInterface $middleware) : StackInterface
    {
        return new self(
          $this->defaultResponse,
          ...array_merge($this->middlewares, [$middleware]));
    }

    public function withoutMiddleware(MiddlewareInterface $middleware) : StackInterface
    {
        return new self(
          $this->defaultResponse,
          ...array_filter(
            $this->middlewares,
            function ($m) use ($middleware) {
                return $middleware !== $m;
            }));
    }

    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    public function process(RequestInterface $request) : ResponseInterface
    {
        $middleware = $this->middlewares[0] ?? false;

        return $middleware
        ? $middleware->process(
          $request,
          $this->obtainDelegateFrame($middleware)
          )
        : $this->defaultResponse;
    }

    public function obtainDelegateFrame(ServerMiddlewareInterface $middleware) : DelegateInterface
    {
        return new class ($this->defaultResponse, $this->withoutMiddleware($middleware)) implements DelegateInterface {
            public function __construct(ResponseInterface $response, StackInterface $stackFrame)
            {
                $this->defaultResponse = $response;
                $this->stackFrame = $stackFrame;
            }

            public function next(RequestInterface $request) : ResponseInterface
            {
                $middleware = $this->stackFrame->getMiddlewares()[0] ?? false;

                return $middleware
                  ? $middleware->process($request, $this->stackFrame->obtainDelegateFrame($middleware))
                  : $this->defaultResponse;
            }
        };
    }
}
