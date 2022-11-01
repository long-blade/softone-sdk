<?php

namespace SoftOne;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use SoftOne\Contracts\ServiceInterface;
use SoftOne\Contracts\SoftOneResponseInterface;
use SoftOne\Http\Response;


class Client
{
    /**
     * A http client that adheres to this (ClientInterface)
     * @var ClientInterface
     */
    protected ClientInterface $httpService;

    /**
     * The default response object
     * @var string
     */
    protected string $responseClass = Response::class;

    public function __construct(ClientInterface $httpService)
    {
        $this->httpService = $httpService;
    }

    /**
     * @param ServiceInterface $service
     * @param SoftOneResponseInterface|null $response A custom response interface (Optional)
     * @return SoftOneResponseInterface
     * @throws GuzzleException
     */
    public function makeRequest(ServiceInterface $service, SoftOneResponseInterface $response = null): SoftOneResponseInterface
    {
        $data['json'] = $service->getData();
        $res = $this->httpService->request($service->method(), $service->endpoint(), $data);

        return is_null($response) ? new Response($res): new $response($res);
    }

    /**
     * Wrapper class for implementing the get request
     *
     * @param ServiceInterface $service
     * @param SoftOneResponseInterface|null $response
     * @return SoftOneResponseInterface
     * @throws GuzzleException
     */
    public static function get(ServiceInterface $service, SoftOneResponseInterface $response = null): SoftOneResponseInterface
    {
        $_this = new static(new \GuzzleHttp\Client([
            'base_uri' =>  Context::$URL
        ]));

       return $_this->makeRequest($service, $response);
    }

}