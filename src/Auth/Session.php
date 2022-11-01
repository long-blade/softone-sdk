<?php

namespace SoftOne\Auth;

use SoftOne\Contracts\SessionInterface;
use SoftOne\Context;

/**
 * Adaptor class. Adapting Context::class to SessionInterface
 * Session is just a client_id string value kept in the context class.
 */
final class Session implements SessionInterface
{
    /**
     * @param string $id
     * @return void
     */
    public function set(string $id): void
    {
        Context::$CLIENT_ID = $id;
    }

    /**
     * @return string|null
     */
    public function get(): ?string
    {
        $clientId = Context::$CLIENT_ID;
        if (is_string($clientId) && strlen($clientId) > 0) {
            return $clientId;
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function update(array $data): void
    {
        foreach ($data as $property => $value) {
            if (property_exists(new Context(), $property)){
                Context::$$property = $value;
            }
        }
    }
}