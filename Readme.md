# PHP Middleware Stack
[![Build Status](https://travis-ci.com/idealo/php-middleware-stack.svg?token=dB3owzyXmEKz9x3RX1AW&branch=master)](https://travis-ci.com/idealo/php-middleware-stack)

This is an implementation of [PSR-15 Draft](https://github.com/php-fig/fig-standards/blob/master/proposed/http-middleware/middleware.md) for PHP7+ runtime environment.

It enables a sequential execution of middlewares that use a PSR-7 conform Response/Request implementation.

## How to
```php

<?php

use Idealo\Middleware\Stack;

$stack = new Stack(
    $defaultResponse,
    $middleware1,
    $middleware2,
    $middleware3
);

$stackResponse = $stack->process($request);


```

## Usage
**idealo/php-middleware-stack** provides the ```Idealo\Middleware\Stack``` class. All it has to know in order to be instantiable is:
* an instance of ```Psr\Http\Message\ResponseInterface``` as the default response
* and middlewares, that implement the ```Psr\Http\Middleware\ServerMiddlewareInterface```

To perform a sequential processing of injected middlewares you have to call stack's ```process``` method with:
* an instance of ```Psr\Http\Message\RequestInterface```.

By default stack's ``process``` method returns the injected response object. If any middleware decides to answer on it's own, than the response object of this certain middleware is returned.

## For example

```php

<?php

// you decide what middleware you want to put in a stack.
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Middleware\DelegateInterface;
use Psr\Http\Middleware\ServerMiddlewareInterface;

class TrickyMiddleware implements ServerMiddlewareInterface
{
    public function process(ServerRequestInterface $request, DelegateInterface $frame) : ResponseInterface
    {
        $requestBody = $request->getBody();
        try{
            // implement your middleware logic here  
        }catch(\Exception $exception){
            return new CustomExceptionResponse($exception);
        }
    
        return $frame->next($request);
    }
}

class VeryTrickyMiddleware implements ServerMiddlewareInterface
{
    ...
}

class LessTrickyMiddleware implements ServerMiddlewareInterface
{
    ...
}

// you define your PSR7 conform response instance
$defaultResponse = new DefaultResponse();

// you put your request into a PSR7 conform way
$request = new ServerRequest();
``
// and here we are
$stack = new \Idealo\Middleware\Stack(
    $defaultResponse,
    new TrickyMiddleware(),
    new VeryTrickyMiddleware(),
    new LessTrickyMiddleware()
);

$stackResponse = $stack->process($request);

// if everything goes well then
var_dump($stackResponse === $defaultResponse); // gives: true

```
