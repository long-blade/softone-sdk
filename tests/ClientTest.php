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
        $guzzleRes = $this->createMock(ResponseInterface::class);
        $guzzleStreamInterface = $this->createMock(StreamInterface::class);

        $guzzleRes->expects($this->exactly(2))
            ->method('getBody')
            ->willReturn($guzzleStreamInterface);

        $guzzleStreamInterface->method('getContents')
            ->willReturn('{"success":false, "errorcode":-2, "error":"Authenticate fails due to invalid credentials."}');

        $response = new Response($guzzleRes);

        $guzzleClient = $this->createMock('GuzzleHttp\Client');
        $guzzleClient->method('request')->willReturn($guzzleRes);
        $client = new Client($guzzleClient);

        $login = $this->createMock(Login::class);
        $login->expects($this->once())
            ->method('method')
            ->willReturn('GET');

        $instanceCallActual = $client->makeRequest($login, $response);
        $this->assertEquals($response, $instanceCallActual);
    }
}
