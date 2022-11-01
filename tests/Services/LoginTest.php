<?php

namespace Services;

use SoftOne\Context;
use SoftOne\Services\Login;
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{

    /** @test */
    public function it_presets_the_username_and_password()
    {
        Context::initialize(
            'https://demo.oncloud.gr/s1services',
            'john',
            'password',
            '2001'
        );

        $loginService = new Login();
        $actual = $loginService->getData();
        $this->assertEquals('john', $actual['username']);
        $this->assertEquals('password', $actual['password']);
    }

    /** @test */
    public function it_can_self_update()
    {
        Context::initialize(
            'https://demo.oncloud.gr/s1services',
            'john',
            'password',
            '2001'
        );

        $loginService = new Login();
        $this->assertEmpty($loginService->getData()['COMPANY']);

        Context::$COMPANY = 'Foo company name';
        $loginService->selfUpdate();
        $this->assertEquals('Foo company name', $loginService->getData()['COMPANY']);

    }
}
