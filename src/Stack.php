<?php

declare(strict_types=1);

namespace Idealo\Middleware;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Stack implements RequestHandlerInterface
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

    private function withoutMiddleware(MiddlewareInterface $middleware): RequestHandlerInterface
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

    public function handle(ServerRequestInterface $request): ResponseInterface
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
