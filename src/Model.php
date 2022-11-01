<?php

namespace SoftOne;

use SoftOne\Contracts\ModelInterface;
use SoftOne\Exception\InaccessiblePropertyException;
use SoftOne\Exception\UndefinedPropertyException;

abstract class Model implements ModelInterface
{
    /**
     * Object properties configuration.
     *
     * An associative array where the keys are the object's property names and
     * the values are their respective default values.
     *
     * When setting a property for which no mutator is set, the new value will
     * be type cast according to the default value.
     *
     * @var array
     */
    protected array $properties = [];

    /**
     * Object properties that cannot be set from outside the object.
     *
     * @var array
     */
    protected array $private = [];

    /**
     * Properties that will throw exception if not provided
     * @var array
     */
    protected array $required = [];

    /**
     * Object data store.
     *
     * @var array Depends on the implementation, array by default.
     */
    protected array $data = [];

    public function __construct()
    {
        $class = get_called_class();
        while ($class = get_parent_class($class)) {
            $this->properties += get_class_vars($class)['properties'];
            $this->required += get_class_vars($class)['required'];
        }

        // Add default data to storage
        if (empty($this->data)) {
            $this->data = $this->properties;
        }
    }

    /**
     * Get object property values.
     *
     * @param string $property
     * @return mixed
     *
     * @throws UndefinedPropertyException If property is not available.
     */
    public function __get(string $property)
    {
       $this->validateGet($property);

        return $this->data[$property];
    }

    /**
     * Magic Set object property values.
     *
     * @param string $key
     * @param mixed $value
     *
     * @throws InaccessiblePropertyException
     * @throws UndefinedPropertyException
     */
    public function __set(string $key, $value)
    {
        $this->validateSet($key, $value);

        // Assign value to property.
       $this->data[$key] = $value;
    }

    /**
     * Set the value of a property in the $properties = []
     * @param string $key
     * @param mixed $value
     * @return void
     *
     * @throws InaccessiblePropertyException
     * @throws UndefinedPropertyException
     */
    protected function set(string $key, $value)
    {
        $this->validateSet($key, $value);

        $this->data[$key] = $value;
    }

    /**
     * Get the property value
     *
     * @param string $key
     * @return mixed
     *
     * @throws UndefinedPropertyException
     */
    protected function get(string $key)
    {
        $this->validateGet($key);

        return $this->data[$key];
    }

    /**
     * Validate that you can set the property value
     *
     * @param string $key
     * @param $value
     * @return void
     *
     * @throws InaccessiblePropertyException
     * @throws UndefinedPropertyException
     */
    protected function validateSet(string $key, $value)
    {
        // Skip unknown properties.
        if (!array_key_exists($key, $this->properties)) {
            throw new UndefinedPropertyException(
                sprintf('Undefined property %s::%s.', static::class, $key)
            );
        }

        // Skip private properties.
        if (in_array($key, $this->private)) {
            throw new InaccessiblePropertyException(
                sprintf('Can\'t set inaccessible property %s::%s.', static::class, $key)
            );
        }

        if (gettype($this->properties[$key]) != gettype($value)) {
            throw new \InvalidArgumentException(
                sprintf('Expected argument of type "%s", "%s" given. %s::%s.',
                    gettype($this->properties[$key]), gettype($value), static::class, $key)
            );
        }
    }

    /**
     * Validate that you can get the property value
     *
     * @param string $key
     * @return void
     *
     * @throws UndefinedPropertyException
     */
    protected function validateGet(string $key)
    {
        // Skip unknown properties.
        if (!array_key_exists($key, $this->data)) {
            throw new UndefinedPropertyException(
                sprintf('Undefined property %s::%s.', static::class, $key)
            );
        }
    }

    /**
     * Retrieve the data that are set on storage ($data = [])
     *
     * @return array|mixed
     */
    public function getData(): array
    {
        return $this->data;
    }
}