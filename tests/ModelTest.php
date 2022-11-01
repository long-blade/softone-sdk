<?php

use SoftOne\Auth\Authorizer;
use SoftOne\Context;
use SoftOne\Exception\InaccessiblePropertyException;
use SoftOne\Exception\MissingApplicationBusinessObjectException;
use SoftOne\Exception\UndefinedPropertyException;
use SoftOne\Model;
use SoftOne\Services\BrowserInfo;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    protected ReflectionClass $modelReflection;

    protected function setUp(): void
    {
        $this->modelReflection = new ReflectionClass(Model::class);

        Context::initialize(
            'https://demo.oncloud.gr/s1services',
            'john',
            'password',
            '2001'
        );

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
    public function it_has_protected_properties_array()
    {
        $browserInfoMock = $this->createMock(BrowserInfo::class);

        $this->assertIsArray($this->properties->getValue($browserInfoMock));
    }

    /** @test */
    public function it_has_protected_private_array()
    {
        $browserInfoMock = $this->createMock(BrowserInfo::class);

        $this->assertIsArray($this->private->getValue($browserInfoMock));
    }

    /** @test */
    public function it_has_protected_data_array()
    {
        $browserInfoMock = $this->createMock(BrowserInfo::class);

        $this->assertIsArray($this->data->getValue($browserInfoMock));
    }

    /** @test */
    public function it_inherits_properties_of_its_child_classes()
    {
        $browserInfoMock = $this->createMock(BrowserInfo::class);

        $this->assertEquals(
            [
                'OBJECT' => '',
                'LIST' => '',
                'VERSION' => 2,
                'LIMIT' => 20,
                'FILTERS' => '',
            ],
            $this->properties->getValue($browserInfoMock)
        );
    }

    /** @test */
    public function it_can_set_and_get_values_for_properties_array()
    {
        $authorizerMock = $this->createMock(Authorizer::class);
        $browser = new BrowserInfo($authorizerMock);

        // Before set
        $this->assertNotEquals('value', $this->data->getValue($browser)['service']);

        // After set
        $this->methodSet->invokeArgs($browser, ['service', 'value']);
        $nowDataArray = $this->data->getValue($browser);
        $this->assertEquals('value', $nowDataArray['service']);

        // use Get method
        $this->assertEquals('value', $this->methodGet->invokeArgs($browser, ['service']));
    }

    /** @test */
    public function it_throws_exception_for_private_properties()
    {
        $authorizerMock = $this->createMock(Authorizer::class);
        $browser = new BrowserInfo($authorizerMock);

        // Put key in private
        $this->private->setValue($browser, ['private_value']);
        // Put it in properties
        $this->properties->setValue($browser, ['private_value' => '']);

        $this->expectException(InaccessiblePropertyException::class);
        $this->methodSet->invokeArgs($browser, ['private_value', 'value']);
    }

    /** @test */
    public function it_throws_exception_for_undefined_properties()
    {
        $authorizerMock = $this->createMock(Authorizer::class);
        $browser = new BrowserInfo($authorizerMock);

        $this->expectException(UndefinedPropertyException::class);
        $this->methodSet->invokeArgs($browser, ['private_value', 'value']);
    }

    /** @test */
    public function it_throws_exception_for_invalid_argument_properties()
    {
        $authorizerMock = $this->createMock(Authorizer::class);
        $browser = new BrowserInfo($authorizerMock);

        // Put it in properties with type string
        $this->properties->setValue($browser, ['public_property' => 'string_type']);

        // try passing a bool value instead of string
        $this->expectException(InvalidArgumentException::class);
        $this->methodSet->invokeArgs($browser, ['public_property', true]);
    }

    /** @test */
    public function it_throws_exception_for_unified_properties()
    {
        $authorizerMock = $this->createMock(Authorizer::class);
        $browser = new BrowserInfo($authorizerMock);

        // Put it in properties with type string
        $this->data->setValue($browser, ['public_property' => 'string_type']);

        // try passing a bool value instead of string
        $this->expectException(UndefinedPropertyException::class);
        $this->methodGet->invokeArgs($browser, ['undefined_property']);
    }

    /** @test */
    public function it_can_retrieve_the_saved_data()
    {
        $authorizerMock = $this->createMock(Authorizer::class);
        $browser = new BrowserInfo($authorizerMock);

        // Put it in properties with type string
        $this->properties->setValue($browser, ['public_property' => '', 'public_property2' => false]);

        //Set e value to the above prop
        $this->methodSet->invokeArgs($browser, ['public_property', 'string_value']);
        $this->methodSet->invokeArgs($browser, ['public_property2', true]);

        $this->assertEquals(
            [
                'public_property' => 'string_value',
                'public_property2' => true,
                'service' => 'getBrowserInfo',
                'clientID' => '',
                'appId' => '2001',
                'OBJECT' => '',
                'LIST' => '',
                'VERSION' => 2,
                'LIMIT' => 20,
                'FILTERS' => '',
            ], $browser->getData()
        );
    }
}
