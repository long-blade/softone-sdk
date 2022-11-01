<?php

namespace SoftOne\Services;


use SoftOne\Auth\Authorizer;
use SoftOne\Auth\Session;
use SoftOne\Client;
use SoftOne\Context;
use SoftOne\Contracts\AuthInterface;
use SoftOne\Exception\InaccessiblePropertyException;
use SoftOne\Exception\UndefinedPropertyException;
use SoftOne\Exception\UninitializedContextException;

class BrowserInfo extends Service
{
    /**
     * @inheritdoc
     */
    protected string $serviceName = 'getBrowserInfo';

    /**
     * @inheritdoc
     */
    protected array $properties = [
        'OBJECT' => '',
        'LIST' => '',
        'VERSION' => 2,
        'LIMIT' => 20,
        'FILTERS' => '',
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
     * @return BrowserInfo
     * @throws InaccessiblePropertyException
     * @throws UndefinedPropertyException
     * @throws UninitializedContextException
     */
    public static function find(): self
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
     * Sets a browser list. This is given by the ERP admins.
     * Reflects LIST key on object request.
     *
     * @param string $list
     * @return $this
     *
     * @throws InaccessiblePropertyException
     * @throws UndefinedPropertyException
     */
    public function withList(string $list): self
    {
        $this->set('LIST', $list);

        return $this;
    }

    /**
     * Set the limit of the returned items.
     *
     * @param int $limit
     * @return $this
     *
     * @throws InaccessiblePropertyException
     * @throws UndefinedPropertyException
     */
    public function limit(int $limit):self
    {
        $this->set('LIMIT', $limit);

        return $this;
    }

    /**
     * Sets the related object like PRODUCT or USER. This is given by the ERP admins.
     * Reflects OBJECT key on object request.
     *
     * @param string $object
     * @return $this
     *
     * @throws InaccessiblePropertyException
     * @throws UndefinedPropertyException
     */
    public function forObject(string $object): self
    {
        $this->set('OBJECT', $object);

        return $this;
    }

    /**
     * Construct filters string.
     * (self)->where(['published' => true], ['published' => 'boolean'])
     *
     * @param array | string $filters
     * @param string $operator
     * @return $this
     */
    public function where($filters, string $operator = '&'): self
    {
        if (is_string($filters)) {
            $this->FILTERS .= $filters;
            return $this;
        }

        $first = true;
        foreach ($filters as $key => $value) {

            $op = $this->_getOperator($key);
            $column = $this->_getColumn($key);

            if (!$first) {
                $this->FILTERS .= "{$operator}{$column}{$op}{$value}";

            }else{
                $this->FILTERS .= "{$column}{$op}{$value}";
                $first = false;
            }
        }

        return $this;
    }

    /**
     * Extract the operation from string. Default =
     * ('foo>' => ">" || "foo" => "=")
     *
     * @param string $key
     * @return string
     */
    protected function _getOperator(string $key): string
    {
        preg_match('/(?:\w+).?(?:\w+)?(?:\s)?([>|<]?=?)/i', $key, $match);

        return trim($match[1] ?: '=');
    }

    /**
     * Extract only the column name. ("foo >=" => "foo")
     *
     * @param string $key
     * @return mixed
     */
    protected function _getColumn(string $key)
    {
        preg_match('/(\w+.?(?:\w+)?)/i', $key, $match);

        return str_replace(' ', '', $match[0]);
//        return $match[0];
    }

    /**
     * @inheritdoc
     */
    public function getResponseDataKeys(): array
    {
       return ['columns', 'rows'];
    }

    /**
     * @inheritdoc
     */
    public function method(): string
    {
        return 'GET';
    }
}