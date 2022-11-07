<?php

namespace Services;

use SoftOne\Auth\Authorizer;
use SoftOne\Context;
use SoftOne\Exception\UninitializedContextException;
use SoftOne\Services\BrowserInfo;
use SoftOne\Services\Data;
use SoftOne\Services\Login;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ServiceTest extends TestCase
{
    protected \ReflectionProperty $reflectedInit;

    protected function setUp(): void
    {
        Context::$CLIENT_ID = '';
        // ReflectionClass is used in this test as IS_INITIALIZED is a private static variable,
        // which would have been set as true due to previous tests
        $reflectedContext = new ReflectionClass(Context::class);
        $reflectedIsInitialized = $reflectedContext->getProperty('IS_INITIALIZED');
        $reflectedIsInitialized->setAccessible(true);

        // make sure appId is set to this value for following tests (ex. :25)
        $reflectedAppID = $reflectedContext->getProperty('APP_ID');
        $reflectedAppID->setAccessible(true);
        $reflectedAppID->setValue('2001');

        $reflectedAppID = $reflectedContext->getProperty('URL');
        $reflectedAppID->setAccessible(true);
        $reflectedAppID->setValue('www.example.com');

        $this->reflectedInit = $reflectedIsInitialized;
    }

    /** @test */
    public function it_throws_exception_if_context_is_uninitialized()
    {
        $this->reflectedInit->setValue(false);

        $this->expectException(UninitializedContextException::class);
        BrowserInfo::find()->forObject('foo');
    }

    /** @test */
    public function it_does_not_perform_authorization_if_session_is_authenticated()
    {
        $this->reflectedInit->setValue(true);

        $authMock = $this->createMock(Authorizer::class);
        $authMock->expects($this->once())
            ->method('isAuthenticated')
            ->willReturn(true);
        $authMock->expects($this->never())->method('authorize');

        // Access it by invoke in order to mock auth
        $browserInstance = new BrowserInfo($authMock);
        $browserInstance->forObject('foo');
    }

    /** @test */
    public function it_performs_authorization_when_session_is_unauthorized()
    {
        $this->reflectedInit->setValue(true);

        $authMock = $this->createMock(Authorizer::class);
        $authMock->expects($this->once())
            ->method('isAuthenticated')
            ->willReturn(false);
        $authMock->expects($this->once())->method('authorize');

        // Access it by invoke in order to mock auth
        $browserInstance = new BrowserInfo($authMock);
        $browserInstance->forObject('foo');
    }

    /** @test */
    public function it_constructs_the_payload()
    {
        $this->reflectedInit->setValue(true);
        $authMock = $this->createMock(Authorizer::class);
        $browserInstance = new BrowserInfo($authMock);

        $browserInstance
            ->forObject('a_business_object')
            ->withList('a_list')
            ->limit(200)
            ->where(['filter_key' => 'filter_value']);

        $this->assertEquals([
            'service' => 'getBrowserInfo',
            'clientID' => '',
            'appId'=> '2001',
            'OBJECT' => 'a_business_object',
            'LIST' => 'a_list',
            'VERSION' => 2,
            'LIMIT' => 200,
            'FILTERS' => 'filter_key=filter_value',
        ], $browserInstance->getData());
    }

    /** @test @dataProvider serviceProvider */
    public function it_creates_service_string_basted_on_class_name($serviceClass, $expected)
    {
        $this->reflectedInit->setValue(true);

        // Create fake authorizer and pass it to the service for test
        $authMock = $this->createMock(Authorizer::class);
        $serviceInstance = new $serviceClass($authMock);

        // Create a reflection class for service instance in order to access protected property
        $reflectionClass = new ReflectionClass($serviceInstance);
        $serviceProp = $reflectionClass->getMethod('service');
        $serviceProp->setAccessible(true);

        $this->assertEquals($expected, $serviceProp->invoke($serviceInstance));
    }

    //TODO: ADD THE REST WHEN CREATE EXTRA SERVICES
    public function serviceProvider(): array
    {
        return [
            [BrowserInfo::class, 'getBrowserInfo'],
            [Data::class, 'setData'],
            [Login::class, 'login'],
        ];
    }

    /** @test */
    public function it_sets_the_clientID()
    {
        $this->reflectedInit->setValue(true);

        $authMock = $this->createMock(Authorizer::class);
        $authMock->expects($this->once())
            ->method('isAuthenticated')
            ->willReturn(true);
        $authMock->expects($this->never())->method('authorize');

        // Access it by invoke in order to mock auth
        $browserInstance = new BrowserInfo($authMock);
        $browserInstance->forObject('foo');
    }
}
