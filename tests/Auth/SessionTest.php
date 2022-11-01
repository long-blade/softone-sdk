<?php

namespace Auth;

use SoftOne\Auth\Session;
use SoftOne\Context;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    /** @test */
    public function it_can_set_client_id()
    {
        //assume that no prev requests have occurred
        Context::$CLIENT_ID = '';
        $session = new Session();

        $this->assertNull($session->get());
        $session->set('id');
        $this->assertEquals('id', $session->get());

        $session->set('');
        $this->assertNull($session->get());
    }

    /** @test */
    public function it_can_update_the_context_keys()
    {
        $session = new Session();

        $data = [
            'COMPANY' => '999',
            'BRANCH' => '1000',
            'MODULE' => '0',
            'REFID' => '300',
            'NOT_EXIST_KEY' => 'value',
        ];

        $this->assertNull(Context::$COMPANY);
        $this->assertNull(Context::$BRANCH);
        $this->assertNull(Context::$MODULE);
        $this->assertNull(Context::$REFID);

        $session->update($data);

        $this->assertEquals('999', Context::$COMPANY);
        $this->assertEquals('1000', Context::$BRANCH);
        $this->assertEquals('0', Context::$MODULE);
        $this->assertEquals('300', Context::$REFID);
    }

}
