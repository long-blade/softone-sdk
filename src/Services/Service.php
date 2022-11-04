<?php

namespace SoftOne\Services;

use SoftOne\Context;
use SoftOne\Contracts\ServiceInterface;
use SoftOne\Exception\InaccessiblePropertyException;
use SoftOne\Exception\UndefinedPropertyException;
use SoftOne\Exception\UninitializedContextException;
use SoftOne\Model;

abstract class Service extends Model implements ServiceInterface
{
    /**
     * Optional field that overrides the service property value
     *  default $this->service()
     *
     * @var string
     */
    protected string $serviceName;

    /**
     * Endpoint for this service
     *
     * @var string
     */
    public string $endpoint = '';

    /**
     * @inheritdoc
     */
    protected array $properties = [
        'service' => '',
        'clientID' => '',
        'appId' => ''
    ];

    /**
     * @throws UninitializedContextException
     * @throws InaccessiblePropertyException
     * @throws UndefinedPropertyException
     */
    public function __construct()
    {
        parent::__construct();

        Context::throwIfUninitialized();

        $this->set('service', $this->service());
        $this->set('clientID', Context::$CLIENT_ID);
        $this->set('appId', Context::$APP_ID);
    }

    /**
     * @inheritdoc
     */
    public function endpoint(): string
    {
        return  $this->endpoint;
    }

    /**
     * Get the Service from class name, or from service property
     *
     * @return string
     */
    private function service(): string
    {
        if (isset($this->serviceName)) {
            return $this->serviceName;
        }

        $namespaceArray = explode('\\', get_class($this));
        return strtolower(end($namespaceArray));
    }
}