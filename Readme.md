# PHP Middleware Stack
[![Build Status](https://travis-ci.com/idealo/php-middleware-stack.svg?branch=master)](https://travis-ci.com/idealo/php-middleware-stack)


This is an implementation of [PSR-15 Draft](https://github.com/php-fig/fig-standards/blob/master/proposed/http-middleware/middleware.md) for PHP7+ runtime environment.

It enables a sequential execution of middlewares that use a PSR-7 conform Response/Request implementations.


## how to
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
