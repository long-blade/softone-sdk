<?php


use SoftOne\Context;
use SoftOne\Exception\UninitializedContextException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Psr\Log\Test\TestLogger;

class ContextTest extends TestCase
{
    public function testCanCreateContext()
    {
        Context::initialize('https://demo.oncloud.gr/s1services','john', 'doe', '2001', '1000', '1000', '0');

        $this->assertEquals('https://demo.oncloud.gr/s1services', Context::$URL);
        $this->assertEquals('john', Context::$USERNAME);
        $this->assertEquals('doe', Context::$PASSWORD);
        $this->assertEquals('2001', Context::$APP_ID);
        $this->assertEquals('1000', Context::$COMPANY);
        $this->assertEquals('1000', Context::$BRANCH);
        $this->assertEquals('0', Context::$MODULE);

        // This should not trigger the exception
        Context::throwIfUninitialized();
    }

    public function testCanUpdateContext()
    {
        Context::initialize('https://demo.oncloud.gr/s1services','john', 'pass', '2020', '1000', '1000', '0');

        $this->assertEquals('https://demo.oncloud.gr/s1services', Context::$URL);
        $this->assertEquals('john', Context::$USERNAME);
        $this->assertEquals('pass', Context::$PASSWORD);
        $this->assertEquals('2020', Context::$APP_ID);
        $this->assertEquals('1000', Context::$COMPANY);
        $this->assertEquals('1000', Context::$BRANCH);
        $this->assertEquals('0', Context::$MODULE);
    }

    public function testThrowsIfUninitialized()
    {
        // ReflectionClass is used in this test as IS_INITIALIZED is a private static variable,
        // which would have been set as true due to previous tests
        $reflectedContext = new ReflectionClass(Context::class);
        $reflectedIsInitialized = $reflectedContext->getProperty('IS_INITIALIZED');
        $reflectedIsInitialized->setAccessible(true);
        $reflectedIsInitialized->setValue(false);

        $this->expectException(UninitializedContextException::class);
        Context::throwIfUninitialized();
    }

    public function testCanAddLogger()
    {
        $testLogger = new TestLogger();

        Context::log('Logg this', LogLevel::DEBUG);
        $this->assertEmpty($testLogger->records);

        Context::$LOGGER = $testLogger;

        Context::log('Defaults to info');
        $this->assertTrue($testLogger->hasInfo('Defaults to info'));

        Context::log('Debug log', LogLevel::DEBUG);
        $this->assertTrue($testLogger->hasDebug('Debug log'));

        Context::log('Info log', LogLevel::INFO);
        $this->assertTrue($testLogger->hasInfo('Info log'));

        Context::log('Notice log', LogLevel::NOTICE);
        $this->assertTrue($testLogger->hasNotice('Notice log'));

        Context::log('Warning log', LogLevel::WARNING);
        $this->assertTrue($testLogger->hasWarning('Warning log'));

        Context::log('Err log', LogLevel::ERROR);
        $this->assertTrue($testLogger->hasError('Err log'));

        Context::log('Crit log', LogLevel::CRITICAL);
        $this->assertTrue($testLogger->hasCritical('Crit log'));

        Context::log('Alert log', LogLevel::ALERT);
        $this->assertTrue($testLogger->hasAlert('Alert log'));

        Context::log('Emerg log', LogLevel::EMERGENCY);
        $this->assertTrue($testLogger->hasEmergency('Emerg log'));
    }
}
