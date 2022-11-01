<?php

namespace SoftOne\Contracts;

interface SoftOneResponseInterface
{
    /**
     * Respond status.
     *
     * @return bool
     */
    public function isSuccess(): bool;

    /**
     * Accepts the service keys to return to the response payload.
     *  (ex. ['rows', 'columns'])
     * @param string[] $keys
     * @return array
     */
    public function data(array $keys): array;

    /**
     * Getter for response body.
     * @return array
     */
    public function body(): array;
}