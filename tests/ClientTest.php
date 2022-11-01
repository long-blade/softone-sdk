<?php

use SoftOne\Client;
use SoftOne\Http\Response;
use SoftOne\Services\Login;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ClientTest extends TestCase
{
    /** @test */
    public function it_can_make_a_request_using_an_http_client()
    {
        $gazzleRes = $this->createMock(ResponseInterface::class);
        $gazzleStreamInterface = $this->createMock(StreamInterface::class);

        $gazzleRes->expects($this->exactly(2))
            ->method('getBody')
            ->willReturn($gazzleStreamInterface);

        $gazzleStreamInterface->method('getContents')
            ->willReturn('{"success":false, "errorcode":-2, "error":"Authenticate fails due to invalid credentials."}');

        $response = new Response($gazzleRes);

        $gazzleClient = $this->createMock('GuzzleHttp\Client');
        $gazzleClient->method('request')->willReturn($gazzleRes);
        $client = new Client($gazzleClient);

        $login = $this->createMock(Login::class);
        $login->expects($this->once())
            ->method('method')
            ->willReturn('GET');

        $instanceCallActual = $client->makeRequest($login, $response);
        $this->assertEquals($response, $instanceCallActual);
    }
}
