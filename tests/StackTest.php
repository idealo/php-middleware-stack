<?php

declare(strict_types=1);

namespace Idealo\Middleware\Tests;

use Idealo\Middleware\Stack;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Idealo\Middleware\Stack
 */
class StackTest extends TestCase
{
    public function testImplementation()
    {
        $response = $this->getResponseMock();

        $this->assertInstanceOf(RequestHandlerInterface::class, new Stack($response));
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

        $response = $this->getResponseMock();

        $stack = new Stack($response, $middleware1, $middleware2, $middleware3);
        $stackResponse = $stack->handle($serverRequest);

        $this->assertInstanceOf(ResponseInterface::class, $stackResponse);
        $this->assertTrue($stackResponse === $response);
    }

    public function testServerEmptyMiddleware()
    {
        $serverRequest = $this->getServerRequestMock();
        $serverRequest
            ->expects($this->exactly(0))
            ->method('getBody');

        $response = $this->getResponseMock();

        $stack = new Stack($response);
        $stackResponse = $stack->handle($serverRequest);

        $this->assertInstanceOf(ResponseInterface::class, $stackResponse);
        $this->assertTrue($stackResponse === $response);
    }

    public function testServerMiddlewareHandlingOrder()
    {
        $callCounter = 0;

        $middleware1 = $this->getMiddlewareMock();
        $middleware1->expects($this->once())
            ->method('process')
            ->willReturnCallback(function (ServerRequestInterface $request, RequestHandlerInterface $handler) use (
                &
                $callCounter
            ) {
                $this->assertEquals(0, $callCounter++);
                return $handler->handle($request);
            });

        $interruptingResponse = $this->getResponseMock();
        $middleware2 = $this->getMiddlewareMock();
        $middleware2->expects($this->once())
            ->method('process')
            ->willReturnCallback(function (ServerRequestInterface $request, RequestHandlerInterface $handler) use (
                $interruptingResponse,
                &$callCounter
            ) {
                $this->assertEquals(1, $callCounter++);
                return $interruptingResponse;
            });

        $middleware3 = $this->getMiddlewareMock();
        $middleware3->expects($this->never())
            ->method('process');

        $stack = new Stack(
            $this->getResponseMock(),
            $middleware1,
            $middleware2,
            $middleware3
        );

        $stack->handle($this->getServerRequestMock());

        $this->assertEquals(2, $callCounter);
    }

    /**
     * @return MockObject|ResponseInterface
     */
    private function getResponseMock()
    {
        return $this->getMockBuilder(ResponseInterface::class)
            ->getMock();
    }

    /**
     * @return MockObject|MiddlewareInterface
     */
    private function getMiddlewareMock()
    {
        return $this->getMockBuilder(MiddlewareInterface::class)
            ->onlyMethods([
                'process',
            ])
            ->getMock();
    }

    /**
     * @return MockObject|ServerRequestInterface
     */
    private function getServerRequestMock()
    {
        return $this->getMockBuilder(ServerRequestInterface::class)
            ->onlyMethods([
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
