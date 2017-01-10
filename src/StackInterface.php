<?php

namespace Idealo\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;

interface StackInterface extends DelegateInterface
{
    /**
     * Return an instance with the specified middleware added to the stack.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the stack, and MUST return an instance that contains
     * the specified middleware.
     *
     * @param MiddlewareInterface $middleware
     *
     * @return self
     */
    public function withMiddleware(MiddlewareInterface $middleware);

    /**
     * Return an instance without the specified middleware.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the stack, and MUST return an instance that does not
     * contain the specified middleware.
     *
     * @param MiddlewareInterface $middleware
     *
     * @return self
     */
    public function withoutMiddleware(MiddlewareInterface $middleware);
}
