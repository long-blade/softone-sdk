<?php

namespace Auth;

use SoftOne\Auth\Authorizer;
use SoftOne\Client;
use SoftOne\Contracts\SessionInterface;
use SoftOne\Context;
use SoftOne\Http\Response;
use SoftOne\Services\Login;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AuthorizerTest extends TestCase
{
    protected ReflectionClass $reflectedContext;

    protected function setUp(): void
    {
        $this->reflectedContext = new ReflectionClass(Context::class);
    }

    public function testIsAuthenticatedIsFalse()
    {
        $session = $this->createMock(SessionInterface::class);
        $client = $this->createMock(Client::class);
        $login = $this->createMock(Login::class);
        $session->expects($this->once())->method('get')->willReturn(null);

        $auth = new Authorizer($session, $client, $login);
        $isAuthenticated = $auth->isAuthenticated();
        $this->assertFalse($isAuthenticated);
    }

    public function testIsAuthenticatedIsTrue()
    {
        $session = $this->createMock(SessionInterface::class);
        $client = $this->createMock(Client::class);
        $login = $this->createMock(Login::class);
        $session->expects($this->once())->method('get')->willReturn('client_id');

        $auth = new Authorizer($session, $client, $login);
        $isAuthenticated = $auth->isAuthenticated();
        $this->assertTrue($isAuthenticated);
    }

    /** @test */
    public function it_will_not_perform_authorize_if_already()
    {
        $reflectedIsInitialized = $this->reflectedContext->getProperty('IS_INITIALIZED');
        $reflectedIsInitialized->setAccessible(true);
        $reflectedIsInitialized->setValue(true);

        $session = $this->createMock(SessionInterface::class);
        $client = $this->createMock(Client::class);
        $login = $this->createMock(Login::class);
        $session->expects($this->once())->method('get')->willReturn('an_id');

        // TODO: look at it again
        $auth = new Authorizer($session, $client, $login);

        $auth->authorize();
    }

    /** @test */
    public function it_can_perform_full_authorization_process_with_minimum_context()
    {
        Context::initialize(
            'https://demo.oncloud.gr/s1services',
            'john',
            'password',
            '2001'
        );

        $session = $this->createMock(SessionInterface::class);
        $client = $this->createMock(Client::class);
        $response = $this->createMock(Response::class);
        $loginService = new Login();

        $session->expects($this->once())->method('set');
        $client->expects($this->exactly(2))
            ->method('makeRequest')
            ->willReturn($response);
        $response->expects($this->exactly(2))
            ->method('isSuccess')
            ->willReturn(true);
        $response->expects($this->any())
            ->method('clientId')
            ->willReturn('1234');
        $response->expects($this->exactly(3))
            ->method('data')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                [
                    // temporary valid response
                    'objs' => [
                        [
                            'COMPANY' => '999',
                            'BRANCH' => '1000',
                            'MODULE' => '0',
                            'REFID' => '300'
                        ]
                    ]
                ],
                [
                    // temporary valid response
                    'objs' => [
                        [
                            'COMPANY' => '999',
                            'BRANCH' => '1000',
                            'MODULE' => '0',
                            'REFID' => '300'
                        ]
                    ]
                ],
                [
                    //valid authorization
                ]
            );

        $auth = new Authorizer($session, $client, $loginService);
        $auth->authorize();
    }
}
