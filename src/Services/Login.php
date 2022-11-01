<?php

namespace SoftOne\Services;


use SoftOne\Context;
use SoftOne\Contracts\LoginInterface;
use SoftOne\Exception\InaccessiblePropertyException;
use SoftOne\Exception\UndefinedPropertyException;

class Login extends Service implements LoginInterface
{
    /**
     * @var string[]
     */
    private array $dataKeys = ['objs'];

    /**
     * @var string
     */
    protected string $method = 'GET';

    /**
     * @inheritdoc
     */
    protected array $properties = [
        'username' => '',
        'password' => '',
        'COMPANY' => 'null',
        'BRANCH' => 'null',
        'MODULE' => 'null',
        'REFID' => 'null',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->selfUpdate();
    }

    /**
     * @return void
     * @throws InaccessiblePropertyException
     * @throws UndefinedPropertyException
     */
    public function selfUpdate()
    {
        // Set the context values
        $this->set('username', Context::$USERNAME ?? '');
        $this->set('password', Context::$PASSWORD ?? '');
        $this->set('COMPANY', Context::$COMPANY ?? '');
        $this->set('BRANCH', Context::$BRANCH ?? '');
        $this->set('MODULE', Context::$MODULE ?? '');
        $this->set('REFID', Context::$REFID ?? '');
    }

    /**
     * @inheritdoc
     */
    public function getResponseDataKeys(): array
    {
        return $this->dataKeys;
    }

    public function method(): string
    {
        return $this->method;
    }
}