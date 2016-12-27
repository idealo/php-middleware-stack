# PHP Middleware Stack
[![Build Status](https://travis-ci.com/idealo/php-middleware-stack.svg?token=dB3owzyXmEKz9x3RX1AW&branch=master)](https://travis-ci.com/idealo/php-middleware-stack)


This is an implementation of [PSR-15 Draft](https://github.com/php-fig/fig-standards/blob/master/proposed/http-middleware/middleware.md) for PHP7+ runtime environment.

It enables a sequential execution of middlewares that use a PSR-7 conform Response/Request implementations.


## How to
```php

<?php

use Idealo\Middleware\Stack;

$stack = new Stack(
  $dafaultResponse,
  $middleware1,
  $middleware2,
  $middleware3);

$stackResponse = $stack->process($request);


```


## Usage
**idealo/php-middleware-stack** provides a ```Idealo\Middleware\Stack``` class. All it has to know in order to be instantiable is:
* an instance of ```Psr\Http\Message\ResponseInterface```
* and Middlewares, that implement a ```Psr\Http\Middleware\ServerMiddlewareInterface```

and in oder to perform a secuential processing of injected middlewares ypo have to call stacks's ```process``` method, with:
* an instance of ```Psr\Http\Message\RequestInterface```.

By default the outcome of stack's process call is an injected to the constuctor Response object. Or if the middleware decides to answer on it's own, than the Response of this certain middleware.

## For example

```php

<?php

// you decide what middleware you want to put in a stack.
use Psr\Http\Middleware\ServerMiddlewareInterface;
use Psr\Http\Middleware\DelegateInterface;
use Psr\Http\Message\ResponseInterface;

class TrickyMiddleware implements ServerMiddlewareInterface
{
    public function process(ServerRequestInterface $request, DelegateInterface $frame) : ResponseInterface
    {
        $requestBody = $request->getBody();
        try{
          // your middlware logic hier  
        }catch(\Exception $e){
          return new CustomExceptionResponse($e);
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

// you put your Request into a PSR7 conform way
$request = new ServerRequest();

// and here we are
$stack = new \Idealo\Middlware\Stack(
  $dafaultResponse,
  new TrickyMiddleware(),
  new VeryTrickyMiddleware(),
  new LessTrickyMiddleware);

$stackResponse = $stack->process($request);

// if everything goes well then
var_dump($stackResponse === $defaultResponse) // gives: true

```
