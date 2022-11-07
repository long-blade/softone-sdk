<?php

namespace SoftOne\Services;

use SoftOne\Auth\Authorizer;
use SoftOne\Auth\Session;
use SoftOne\Client;
use SoftOne\Context;
use SoftOne\Contracts\AuthInterface;
use SoftOne\Contracts\ServiceInterface;
use SoftOne\Exception\InaccessiblePropertyException;
use SoftOne\Exception\UndefinedPropertyException;
use SoftOne\Exception\UninitializedContextException;

class Data extends Service
{
    /**
     * @inheritdoc
     */
    protected string $serviceName = 'setData';

    /**
     * @inheritdoc
     */
    protected array $properties = [
        'OBJECT' => '',
        'KEY' => '',
        'LOCATEINFO' => '',
        'data' => [],
    ];

    /**
     * @param AuthInterface $authorizer
     * @throws InaccessiblePropertyException
     * @throws UndefinedPropertyException
     * @throws UninitializedContextException
     */
    public function __construct(AuthInterface $authorizer)
    {
        if (!$authorizer->isAuthenticated()) {
            $authorizer->authorize();
        }

        parent::__construct();
    }

    /**
     * @return ServiceInterface
     * @throws InaccessiblePropertyException
     * @throws UndefinedPropertyException
     * @throws UninitializedContextException
     */
    protected static function init(): ServiceInterface
    {
        return new static(new Authorizer(
                new Session(),
                new Client(new \GuzzleHttp\Client([
                    'base_uri' =>  Context::$URL
                ])),
                new Login())
        );
    }

    /**
     * Insert data to the "data" property
     *
     * @param string $businessObject
     * @param array $data
     * @return static
     * @throws InaccessiblePropertyException
     * @throws UndefinedPropertyException
     * @throws UninitializedContextException
     */
    public static function insert(string $businessObject, array $data): ServiceInterface
    {
        $_this = self::init();
        $_this->set('OBJECT', $businessObject);
        $_this->set('data', [$businessObject => $data]);

        return $_this;
    }


    public static function update(string $businessObject, string $key, array $data)
    {
        $_this = self::init();
        $_this->set('OBJECT', $businessObject);
        $_this->set('KEY', $key);
        $_this->set('data', [$businessObject => $data]);

        return $_this;
    }

    /**
     * Add extra data to the data property.
     * https://www.softone.gr/ws/#setData
     *
     * @param string $businessObject
     * @param array $extraData
     * @return $this
     * @throws InaccessiblePropertyException
     * @throws UndefinedPropertyException
     */
    public function addExtra(string $businessObject, array $extraData)
    {
        $data = $this->get('data');
        $extra = [$businessObject => $extraData];

        $this->set('data', array_merge($data, $extra));

        return $this;
    }

    public function getResponseDataKeys(): array
    {
        return ['id'];
    }

    public function method(): string
    {
        return 'post';
    }
}