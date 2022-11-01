<?php

namespace SoftOne\Http;

use SoftOne\Contracts\SoftOneResponseInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Adaptor response class
 */
class Response implements SoftOneResponseInterface
{
    protected ResponseInterface $response;

    protected array $body;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
        $content = $this->response->getBody()->getContents();

        $content = iconv(
            mb_detect_encoding($content),
            'UTF-8//IGNORE',
            $content
        );

        $this->body = json_decode($content, true);
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        if (isset($this->body['success'])) {
            return $this->body['success'];
        }

        return false;
    }

    /**
     * Accepts the service keys to return to the response payload.
     *  (ex. ['rows', 'columns'])
     * @param string[] $keys
     * @return array
     */
    public function data(array $keys): array
    {
        $data = [];
        if ($this->isSuccess()) {
            foreach ($keys as $key) {
                if (isset($this->body[$key])) {
                    $data[$key] = $this->body[$key]; //TODO: maybe perform data validation first
                }
            }
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function body(): array
    {
        if ($this->isSuccess()) {
            return $this->body;
        }

        return [];
    }

    /**
     * Client key for authentication requests.
     * THis method can be deprecated. We can use the $r->data(['clientId'])
     * @return string
     */
    public function clientId(): string
    {
        if ($this->isSuccess() && isset($this->body['clientID'])) {
            return $this->body['clientID'];
        }

        return '';
    }
}