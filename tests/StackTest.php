<?php

use PHPUnit\Framework\TestCase;
use Idealo\Middleware\Stack;
use Psr\Http\Middleware\StackInterface;
use Psr\Http\Middleware\ServerMiddlewareInterface;
use Psr\Http\Middleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class StackTest extends TestCase
{
    public function testImplementation()
    {
        $response = $this->getMockBuilder(ResponseInterface::class)
      ->getMock();
        $this->assertInstanceOf(StackInterface::class, new Stack($response));
    }

    public function testServerMiddlewareStack()
    {
        $middleware1 = new MiddlewareStub();
        $middleware2 = new MiddlewareStub();
        $middleware3 = new MiddlewareStub();

        $serverRequest = $this->getServerRequestMock();

        $serverRequest
      ->expects($this->exactly(3))
      ->method('getBody');

        $response = $this->getMockBuilder(ResponseInterface::class)
      ->getMock();

        $stack = new Stack($response, $middleware1, $middleware2, $middleware3);
        $stackResponse = $stack->process($serverRequest);
        $this->assertInstanceOf(ResponseInterface::class, $stackResponse);
    }

    private function getServerRequestMock()
    {
        return $this->getMockBuilder(ServerRequestInterface::class)
      ->setMethods([
      'getServerParams',
      'getCookieParams',
      'withCookieParams',
      'getQueryParams',
      'withQueryParams',
      'getUploadedFiles',
      'withUploadedFiles',
      'getParsedBody',
      'withParsedBody',
      'getAttributes',
      'getAttribute',
      'withAttribute',
      'withoutAttribute',
      'getRequestTarget',
      'withRequestTarget',
      'getMethod',
      'withMethod',
      'getUri',
      'withUri',
      'getProtocolVersion',
      'withProtocolVersion',
      'getHeaders',
      'hasHeader',
      'getHeader',
      'getHeaderLine',
      'withHeader',
      'withAddedHeader',
      'withoutHeader',
      'getBody',
      'withBody',
      ])->getMock();
    }
}

class MiddlewareStub implements ServerMiddlewareInterface
{
    public function process(ServerRequestInterface $request, DelegateInterface $frame) : ResponseInterface
    {
        $request->getBody();

        return $frame->next($request);
    }
}