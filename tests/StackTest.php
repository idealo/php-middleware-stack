<?php

use Idealo\Middleware\Stack;
use Idealo\Middleware\StackInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Idealo\Middleware\Stack
 */
class StackTest extends TestCase
{
    public function testImplementation()
    {
        $response = $this->getResponseMock();

        $this->assertInstanceOf(DelegateInterface::class, new Stack($response));
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
        $stackResponse = $stack->process($serverRequest);

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
        $stackResponse = $stack->process($serverRequest);

        $this->assertInstanceOf(ResponseInterface::class, $stackResponse);
        $this->assertTrue($stackResponse === $response);
    }

    public function testServerMiddlewareProcessingOrder()
    {
        $callCounter = 0;

        $middleware1 = $this->getMiddlewareMock();
        $middleware1->expects($this->once())
            ->method('process')
            ->willReturnCallback(function (ServerRequestInterface $request, DelegateInterface $frame) use (&$callCounter
            ) {
                $this->assertEquals(0, $callCounter++);
                return $frame->process($request);
            });

        $interruptingResponse = $this->getResponseMock();
        $middleware2 = $this->getMiddlewareMock();
        $middleware2->expects($this->once())
            ->method('process')
            ->willReturnCallback(function (ServerRequestInterface $request, DelegateInterface $frame) use (
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

        $stack->process($this->getServerRequestMock());

        $this->assertEquals(2, $callCounter);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ResponseInterface
     */
    private function getResponseMock()
    {
        return $this->getMockBuilder(ResponseInterface::class)
            ->getMock();
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|MiddlewareInterface
     */
    private function getMiddlewareMock()
    {
        return $this->getMockBuilder(MiddlewareInterface::class)
            ->setMethods([
                'process',
            ])
            ->getMock();
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ServerRequestInterface
     */
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

class MiddlewareStub implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, DelegateInterface $frame): ResponseInterface
    {
        $body = $request->getBody();

        return $frame->process($request);
    }
}

class MiddlewareResponseStub implements MiddlewareInterface
{
    /**
     * @var ResponseInterface
     */
    private $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $frame): ResponseInterface
    {
        $body = $request->getBody();

        return $this->response;
    }
}
