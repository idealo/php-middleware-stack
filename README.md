# PHP Middleware Stack
[![Build Status](https://github.com/idealo/php-middleware-stack/workflows/CI/badge.svg)](https://github.com/idealo/php-middleware-stack/actions?query=workflow%3Aci)
[![Maintainability](https://api.codeclimate.com/v1/badges/254d91c39447f58c7d44/maintainability)](https://codeclimate.com/github/idealo/php-middleware-stack/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/254d91c39447f58c7d44/test_coverage)](https://codeclimate.com/github/idealo/php-middleware-stack/test_coverage)
[![Packagist](https://img.shields.io/packagist/v/idealo/php-middleware-stack)](https://packagist.org/packages/idealo/php-middleware-stack)

This is an implementation of [PSR-15](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-15-request-handlers.md) using the proposed Interface packages [psr/http-server-middleware](https://github.com/php-fig/http-server-middleware) and [psr/http-server-handler](https://github.com/php-fig/http-server-handler) for PHP7+ runtime environment.

It enables a sequential execution of middlewares that use a PSR-7 conform Response/Request implementation.

## Install

```bash 
composer require idealo/php-middleware-stack
```

Note: use Version ^1.0 for PHP < 7.3

## How to
```php
use Idealo\Middleware\Stack;

$stack = new Stack(
    $defaultResponse,
    $middleware1,
    $middleware2,
    $middleware3
);

$stackResponse = $stack->handle($request);
```

## Usage
**idealo/php-middleware-stack** provides the ```Idealo\Middleware\Stack``` class. All it has to know in order to be instantiable is:
* an instance of ```Psr\Http\Message\ResponseInterface``` as the default response
* and middlewares, that implement the ```Psr\Http\Server\MiddlewareInterface```

To perform a sequential processing of injected middlewares you have to call stack's ```handle``` method with:
* an instance of ```Psr\Http\Message\ServerRequestInterface```.

By default stack's ```handle``` method returns the injected response object. If any middleware decides to answer on it's own, than the response object of this certain middleware is returned.

Stack implements ```Psr\Http\Server\RequestHandlerInterface```.

## For example

```php
// you decide what middleware you want to put in a stack.
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

class TrickyMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $requestBody = $request->getBody();
        try {
            // implement your middleware logic here  
        } catch (\Exception $exception){
            return new CustomExceptionResponse($exception);
        }
    
        return $handler->handle($request);
    }
}

class VeryTrickyMiddleware implements MiddlewareInterface
{
    ...
}

class LessTrickyMiddleware implements MiddlewareInterface
{
    ...
}

// you define your PSR7 conform response instance
$defaultResponse = new DefaultResponse();

// you put your request into a PSR7 conform way
$request = new ServerRequest();

// and here we are
$stack = new \Idealo\Middleware\Stack(
    $defaultResponse,
    new TrickyMiddleware(),
    new VeryTrickyMiddleware(),
    new LessTrickyMiddleware()
);

$stackResponse = $stack->handle($request);

// if everything goes well then
var_dump($stackResponse === $defaultResponse); // gives: true
```
