<?php

namespace Services;

use SoftOne\Auth\Authorizer;
use SoftOne\Context;
use SoftOne\Services\BrowserInfo;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class BrowserInfoTest extends TestCase
{
    protected function setUp(): void
    {
        Context::initialize(
            'https://demo.oncloud.gr/s1services',
            'john',
            'password',
            '2001'
        );
        $this->modelReflection = new ReflectionClass(BrowserInfo::class);

        $this->modelReflection->hasProperty('properties');
        $this->properties = $this->modelReflection->getProperty('properties');
        $this->properties->setAccessible(true);

        $this->modelReflection->hasProperty('data');
        $this->data = $this->modelReflection->getProperty('data');
        $this->data->setAccessible(true);

        $this->modelReflection->hasProperty('private');
        $this->private = $this->modelReflection->getProperty('private');
        $this->private->setAccessible(true);

        $this->methodSet = $this->modelReflection->getMethod('set');
        $this->methodSet->setAccessible(true);

        $this->methodGet = $this->modelReflection->getMethod('get');
        $this->methodGet->setAccessible(true);
    }

    /** @test */
    public function it_has_specific_object_properties_set()
    {
        $browserInfoMock = $this->createMock(BrowserInfo::class);
        $actual = $this->properties->getValue($browserInfoMock);
        $expected = [
            'OBJECT' => '',
            'LIST' => '',
            'VERSION' => 2,
            'LIMIT' => 20,
            'FILTERS' => '',
        ];

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_performs_authorization()
    {
        $auth = $this->createMock(Authorizer::class);
        $auth->expects($this->once())->method('isAuthenticated')->willReturn(false);
        $auth->expects($this->once())->method('authorize');

        new BrowserInfo($auth);
    }

    /** @test */
    public function it_can_set_a_list()
    {
        $auth = $this->createMock(Authorizer::class);
        $browser = new BrowserInfo($auth);
        $browser->withList('this_is_a_list');

        $this->assertEquals('this_is_a_list', $browser->LIST);
    }

    /** @test */
    public function it_can_set_an_object()
    {
        $auth = $this->createMock(Authorizer::class);
        $browser = new BrowserInfo($auth);
        $browser->forObject('ITEM_OBJECT');

        $this->assertEquals('ITEM_OBJECT', $browser->OBJECT);
    }

    /** @test @dataProvider filtersProvider */
    public function it_can_set_filters($input, $expected)
    {
        $auth = $this->createMock(Authorizer::class);
        $browser = new BrowserInfo($auth);
        $browser->forObject('foo')->where($input);

        $this->assertEquals($expected, $browser->FILTERS);
    }

    public function filtersProvider(): array
    {
        return [
            ['CUSTOMER.CODE=30*&CUSTOMER.AFM=046156989', 'CUSTOMER.CODE=30*&CUSTOMER.AFM=046156989'],
            [['qDate' => '1/1/2020'], 'qDate=1/1/2020'],
            [['qDate >=' => '1/1/2020'], 'qDate>=1/1/2020'],
            [['CUSTOMER.EMAIL' => 'mail@mail.gr'], 'CUSTOMER.EMAIL=mail@mail.gr'],
            [['CUSTOMER.EMAIL >=' => 'mail@mail.gr'], 'CUSTOMER.EMAIL>=mail@mail.gr'],
            [['qDate' => '1/1/2020', 'foo >' => 'bar'], 'qDate=1/1/2020&foo>bar'],
            [['qDate' => '1/1/2020', 'foo >' => 'bar', 'baz>=' => 'boo'], 'qDate=1/1/2020&foo>bar&baz>=boo'],
        ];
    }

    /** @test */
    public function it_can_set_limit()
    {
        $auth = $this->createMock(Authorizer::class);
        $browser = new BrowserInfo($auth);
        $browser->limit(200);

        $this->assertEquals(200, $browser->LIMIT);
    }
}
