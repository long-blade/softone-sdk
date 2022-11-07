<?php

namespace Http;


use SoftOne\Http\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ResponseTest extends TestCase
{
    protected function setUp(): void
    {
        setlocale(LC_CTYPE, 'en_US.utf8');
    }

    public function httpResponsesForSuccess()
    {
        return [
            ['{"success":false, "errorcode":-2, "error":"Authenticate fails due to invalid credentials."}', false],
            ['{"success": true, "clientID":"1234", "objs":[{"COMPANY" : "999","COMPANYNAME" : "BAREFOOT LUXURY LIVING ���","BRANCH" : "1000","BRANCHNAME" : "Main Store","MODULE" : "0","MODULENAME" : "�������","REFID" : "300","REFIDNAME" : "Web User","USERID" : "300","FINALDATE" : "","ROLES" : "","XSECURITY" : "","EXPTIME" : ""}], "ver":"6.00.622.11517", "sn":"01104316905021", "off":false, "pin":false, "appid":"1000"}', true],
        ];
    }

    public function httpResponsesForId()
    {
        return [
            ['{"success":false, "errorcode":-2, "error":"Authenticate fails due to invalid credentials."}', ''],
            ['{"success": true, "clientID":"1234", "objs":[{"COMPANY" : "999","COMPANYNAME" : "BAREFOOT LUXURY LIVING ���","BRANCH" : "1000","BRANCHNAME" : "Main Store","MODULE" : "0","MODULENAME" : "�������","REFID" : "300","REFIDNAME" : "Web User","USERID" : "300","FINALDATE" : "","ROLES" : "","XSECURITY" : "","EXPTIME" : ""}], "ver":"6.00.622.11517", "sn":"01104316905021", "off":false, "pin":false, "appid":"1000"}', '1234'],
        ];
    }

    public function httpResponsesForData()
    {
        return [
            ['{"success":false, "errorcode":-2, "error":"Authenticate fails due to invalid credentials."}', []],
            [
                '{"success": true, "clientID":"1234", "objs":[{"COMPANY" : "999","COMPANYNAME" : "BAREFOOT LUXURY LIVING ΙΚΕ","BRANCH" : "1000","BRANCHNAME" : "Main Store","MODULE" : "0","MODULENAME" : "Χρήστης","REFID" : "300","REFIDNAME" : "Web User","USERID" : "300","FINALDATE" : "","ROLES" : "","XSECURITY" : "","EXPTIME" : ""}], "ver":"6.00.622.11517", "sn":"01104316905021", "off":false, "pin":false, "appid":"1000"}',
                [
                    'objs' => [
                        0 => [
                            'COMPANY' => '999',
                            'COMPANYNAME' => 'BAREFOOT LUXURY LIVING ΙΚΕ',
                            'BRANCH' => '1000',
                            'BRANCHNAME' => 'Main Store',
                            'MODULE' => '0',
                            'MODULENAME' => 'Χρήστης',
                            'REFID' => '300',
                            'REFIDNAME' => 'Web User',
                            'USERID' => '300',
                            'FINALDATE' => '',
                            'ROLES' => '',
                            'XSECURITY' => '',
                            'EXPTIME' => '',
                        ],
                    ],
                ]
            ],
        ];
    }

    /** @test @dataProvider httpResponsesForSuccess */
    public function it_constructs_a_response($input, $expected)
    {
        $guzzleResponse = $this->createMock(ResponseInterface::class);
        $guzzleStreamInterface = $this->createMock(StreamInterface::class);

        $guzzleResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($guzzleStreamInterface);

        $guzzleStreamInterface->method('getContents')
            ->willReturn($input);

        $response = new Response($guzzleResponse);
        $this->assertEquals($expected, $response->isSuccess());
    }

    /** @test @dataProvider httpResponsesForId */
    public function it_can_get_the_client_id_returned($input, $expected)
    {
        $guzzleResponse = $this->createMock(ResponseInterface::class);
        $guzzleStreamInterface = $this->createMock(StreamInterface::class);

        $guzzleResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($guzzleStreamInterface);

        $guzzleStreamInterface->method('getContents')
            ->willReturn($input);

        $response = new Response($guzzleResponse);
        $this->assertEquals($expected, $response->clientId());
    }

    /** @test @dataProvider httpResponsesForData */
    public function it_can_create_array_of_data_for_successful_responses($input, $expected)
    {
        $guzzleResponse = $this->createMock(ResponseInterface::class);
        $guzzleStreamInterface = $this->createMock(StreamInterface::class);

        $guzzleResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($guzzleStreamInterface);

        $guzzleStreamInterface->method('getContents')
            ->willReturn($input);

        $response = new Response($guzzleResponse);
        $this->assertEquals($expected, $response->data(['objs']));
    }

    /** @test @dataProvider httpResponsesForData */
    public function it_skips_undefined_body_keys($input, $expected)
    {
        $guzzleResponse = $this->createMock(ResponseInterface::class);
        $guzzleStreamInterface = $this->createMock(StreamInterface::class);

        $guzzleResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($guzzleStreamInterface);

        $guzzleStreamInterface->method('getContents')
            ->willReturn($input);

        $response = new Response($guzzleResponse);
        $this->assertEmpty($response->data(['undefined']));
    }

    /** @test */
    public function it_will_return_false_for_requests_without_success_property_in_response_payload()
    {
        $guzzleResponse = $this->createMock(ResponseInterface::class);
        $guzzleStreamInterface = $this->createMock(StreamInterface::class);

        $guzzleResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($guzzleStreamInterface);

        $guzzleStreamInterface->method('getContents')
            ->willReturn('{}'); // mo response
        $softOneRes = new Response($guzzleResponse);

        $this->assertEquals([], $softOneRes->body());
        $this->assertFalse($softOneRes->isSuccess());
    }

    /** @test */
    public function it_accepts_object_syntax_for_body_properties()
    {
        $guzzleResponse = $this->createMock(ResponseInterface::class);
        $guzzleStreamInterface = $this->createMock(StreamInterface::class);

        $guzzleResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($guzzleStreamInterface);
        $guzzleStreamInterface->method('getContents')
            ->willReturn('{"success": true,"id": 1480}');


        $response = new Response($guzzleResponse);

        $this->assertEquals('1480', $response->id);
    }
}
