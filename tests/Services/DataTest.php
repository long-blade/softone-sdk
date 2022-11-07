<?php

namespace Services;

use PHPUnit\Framework\TestCase;
use SoftOne\Auth\Authorizer;
use SoftOne\Context;
use SoftOne\Contracts\ServiceInterface;
use SoftOne\Services\BrowserInfo;
use SoftOne\Services\Data;

class DataTest extends TestCase
{
    protected function setUp(): void
    {
        Context::initialize(
            'https://demo.oncloud.gr/s1services',
            'john',
            'password',
            '2001'
        );

        $this->service = new \ReflectionClass(Data::class);

        // Making protected $properties array accessible
        if ($this->service->hasProperty('properties')) {
            $this->properties = $this->service->getProperty('properties');
            $this->properties->setAccessible(true);
        }

    }

    /** @test */
    public function it_returns_a_service_instance()
    {
        $request = Data::insert('object', []);

        $this->assertInstanceOf(ServiceInterface::class, $request);
    }

    /** @test */
    public function it_performs_authorization()
    {
        $auth = $this->createMock(Authorizer::class);
        $auth->expects($this->once())->method('isAuthenticated')->willReturn(false);
        $auth->expects($this->once())->method('authorize');

        new Data($auth);
    }


    /** @test */
    public function it_has_the_correct_service_name()
    {
        $request = Data::insert('CUSTOMER',['foo' => 'bar', 'foobar' => 'baz']);

        // make private method service accessible for test
        $serviceMethod = $this->service->getMethod('service');
        $serviceMethod->setAccessible(true);

        // Invoke method service after make it accessible
        $actualServiceName = $serviceMethod->invoke($request);

        $this->assertEquals('setData', $actualServiceName);
    }

    /** @test */
    public function it_has_the_correct_service_related_properties()
    {
        $setDataMock = $this->createMock(Data::class);
        $actual = $this->properties->getValue($setDataMock);
        $expected = [
            'OBJECT' => '',
            'KEY' => '',
            'LOCATEINFO' => '',
            'data' => [],
        ];

        $this->assertEquals($expected, $actual);
    }

    /** @test*/
    public function it_can_set_object_property_type()
    {
        $request = Data::insert('BUSINESS_OBJECT', []);

        $this->assertEquals('BUSINESS_OBJECT', $request->getData()['OBJECT']);
    }

    /** @test */
    public function it_can_the_request_data()
    {
        $request = Data::insert('BUSINESS_OBJECT',['foo' => 'bar', 'foobar' => 'baz']);

        $expected = ['BUSINESS_OBJECT' => ['foo' => 'bar', 'foobar' => 'baz']];

        $this->assertEquals($expected, $request->getData()['data']);
    }

    /** @test */
    public function it_can_add_extra_data()
    {
        $request = Data::insert('BUSINESS_OBJECT',['foo' => 'bar', 'foobar' => 'baz'])
        ->addExtra('BUSINESS_OBJECT_EXTRA', ['foo' => 'bar', 'foobar' => 'baz']);

        $expected = [
            'BUSINESS_OBJECT' => ['foo' => 'bar', 'foobar' => 'baz'],
            'BUSINESS_OBJECT_EXTRA' => ['foo' => 'bar', 'foobar' => 'baz']
        ];

        $this->assertEquals($expected, $request->getData()['data']);
    }

    /** @test */
    public function it_can_perform_update()
    {
        $request = Data::update('BUSINESS_OBJECT', '45', ['foo' => 'bar', 'foobar' => 'baz']);

        $this->assertEquals('45', $request->getData()['KEY']);
    }
}
