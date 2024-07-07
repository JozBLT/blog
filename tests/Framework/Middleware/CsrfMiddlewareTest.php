<?php

namespace Tests\Framework\Middleware;

use Exception;
use Framework\Exception\CsrfInvalidException;
use Framework\Middleware\CsrfMiddleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Random\RandomException;

class CsrfMiddlewareTest extends TestCase
{

    private CsrfMiddleware $middleware;
    private array $session;

    public function setUp(): void
    {
        $this->session = [];
        $this->middleware = new CsrfMiddleware($this->session);
    }

    /** @throws Exception */
    public function testLetGetRequestPass()
    {
        $delegate = $this->getMockBuilder(RequestHandlerInterface::class)
            ->onlyMethods(['handle'])
            ->getMock();

        $delegate->expects($this->once())
            ->method('handle')
            ->willReturn(new Response());

        $request = (new ServerRequest('GET', '/demo'));
        $this->middleware->process($request, $delegate);
    }

    /** @throws Exception */
    public function testBlockPostRequestWithoutCsrf()
    {
        $delegate = $this->getMockBuilder(RequestHandlerInterface::class)
            ->onlyMethods(['handle'])
            ->getMock();

        $delegate->expects($this->never())
            ->method('handle');

        $request = (new ServerRequest('POST', '/demo'));
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $delegate);
    }

    /**
     * @throws RandomException
     * @throws Exception
     */
    public function testBlockPostRequestWithInvalidCsrf()
    {
        $delegate = $this->getMockBuilder(RequestHandlerInterface::class)
            ->onlyMethods(['handle'])
            ->getMock();

        $delegate->expects($this->never())
            ->method('handle');

        $this->middleware->generateToken();
        $request = (new ServerRequest('POST', '/demo'));
        $request = $request->withParsedBody(['_csrf' => 'blablu']);
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $delegate);
    }

    /**
     * @throws RandomException
     * @throws Exception
     */
    public function testLetPostWithTokenPass()
    {
        $delegate = $this->getMockBuilder(RequestHandlerInterface::class)
            ->onlyMethods(['handle'])
            ->getMock();

        $delegate->expects($this->once())
            ->method('handle')
            ->willReturn(new Response());

        $request = (new ServerRequest('POST', '/demo'));
        $token = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->middleware->process($request, $delegate);
    }

    /**
     * @throws RandomException
     * @throws Exception
     */
    public function testLetPostWithTokenPassOnce()
    {
        $delegate = $this->getMockBuilder(RequestHandlerInterface::class)
            ->onlyMethods(['handle'])
            ->getMock();

        $delegate->expects($this->once())
            ->method('handle')
            ->willReturn(new Response());

        $request = (new ServerRequest('POST', '/demo'));
        $token = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->middleware->process($request, $delegate);
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $delegate);
    }

    /** @throws RandomException */
    public function testLimitTokenNumber()
    {
        for ($i = 0; $i < 100; ++$i) {
            $token = $this->middleware->generateToken();
        }
        $this->assertCount(50, $this->session['csrf']);
        $this->assertEquals($token, $this->session['csrf'][49]);
    }

}
