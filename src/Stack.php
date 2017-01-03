<?php

namespace Idealo\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Middleware\DelegateInterface;
use Psr\Http\Middleware\MiddlewareInterface;
use Psr\Http\Middleware\ServerMiddlewareInterface;
use Psr\Http\Middleware\StackInterface;

class Stack implements StackInterface
{
    /**
     * @var ServerMiddlewareInterface[]
     */
    protected $middlewares = [];

    /**
     * @var ResponseInterface
     */
    protected $defaultResponse;

    public function __construct(ResponseInterface $response, ServerMiddlewareInterface ...$middlewares)
    {
        $this->defaultResponse = $response;
        $this->middlewares = $middlewares;
    }

    public function withMiddleware(MiddlewareInterface $middleware): StackInterface
    {
        return new self(
            $this->defaultResponse,
            ...array_merge(
                $this->middlewares,
                [$middleware]
            )
        );
    }

    public function withoutMiddleware(MiddlewareInterface $middleware): StackInterface
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

    public function process(RequestInterface $request): ResponseInterface
    {
        $middleware = $this->middlewares[0] ?? false;

        return $middleware
            ? $middleware->process(
                $request,
                $this->obtainDelegateFrame($middleware)
            )
            : $this->defaultResponse;
    }

    protected function obtainDelegateFrame(ServerMiddlewareInterface $middleware): DelegateInterface
    {
        $stackFrame = $this->withoutMiddleware($middleware);

        return new class ($stackFrame) implements DelegateInterface
        {
            /**
             * @var StackInterface
             */
            private $stackFrame;

            public function __construct(StackInterface $stackFrame)
            {
                $this->stackFrame = $stackFrame;
            }

            public function next(RequestInterface $request): ResponseInterface
            {
                return $this->stackFrame->process($request);
            }
        };
    }
}
