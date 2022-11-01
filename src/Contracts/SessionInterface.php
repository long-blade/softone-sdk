<?php

namespace SoftOne\Contracts;

interface SessionInterface
{
    /**
     * Set an id or token
     *
     * @param string $id
     * @return void
     */
    public function set(string $id): void;

    /**
     * Get an id or token
     *
     * @return string|null
     */
    public function get(): ?string;

    /**
     * Can update extra session data
     *
     * @param array $data
     * @return void
     */
    public function update(array $data): void;
}