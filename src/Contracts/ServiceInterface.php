<?php

namespace SoftOne\Contracts;

interface ServiceInterface
{
    /**
     * An array of string keys to pick as a response data.
     *  Ex. $keys = ['rows', 'columns'] will return an array of $keys with their data as values
     *
     * @return string[]
     */
    public function getResponseDataKeys(): array;


    /**
     * Service method (ex. GET, POST, PUTT, DELETE)
     *
     * @return string
     */
    public function method(): string;

    /**
     * The Service Endpoint
     *
     * @return string
     */
    public function endpoint(): string;
}