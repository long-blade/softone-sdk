<?php

namespace SoftOne\Auth;

use GuzzleHttp\Exception\GuzzleException;
use SoftOne\Client;
use SoftOne\Contracts\AuthInterface;
use SoftOne\Contracts\ServiceInterface;
use SoftOne\Contracts\SessionInterface;
use SoftOne\Exception\MaxAuthorizationAttemptsExited;
use SoftOne\Http\Response;

/**
 * Service class that orchestrates the authorization process
 */
class Authorizer implements AuthInterface
{
    const MAX_ATTEMPTS = 5;

    private SessionInterface $session;
    private Client $client;
    private ServiceInterface $loginService;
    private int $attempts = 0;

    public function __construct(
        SessionInterface $session,
        Client $client,
        ServiceInterface $loginService
    )
    {
        $this->session = $session;
        $this->client = $client;
        $this->loginService = $loginService;
    }

    /**
     * Check if we
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return !is_null($this->session->get());
    }


    /**
     * @throws MaxAuthorizationAttemptsExited
     * @throws GuzzleException
     */
    public function authorize()
    {
        if ($this->isAuthenticated()) {
            return;
        }

        if ($this->attempts >= self::MAX_ATTEMPTS) {
            throw new MaxAuthorizationAttemptsExited(
                sprintf('MaxAuthorizationAttemptsExited %s::%s.', static::class, 'authorize()')
            );
        }

        $dataKeys = $this->loginService->getResponseDataKeys();
        $this->loginService->selfUpdate();
        $response = $this->client->makeRequest($this->loginService);

        if ($this->isTemporaryResponse($response)){
            $data = $response->data($dataKeys)['objs'][0]; // TODO: make it more solid
            $this->session->update($data);

            $this->attempts += 1;
            $this->authorize();
        }else {
            $this->session->set($response->clientId());
        }
    }

    /**
     * Todo: Is this class responsible for validating clientId?
     *  may be abstract method to a response validator
     *
     * Check if the response is temporary (the login obj is not complete)
     *
     * @param Response $response
     * @return bool
     */
    protected function isTemporaryResponse(Response $response): bool
    {
        $dataKeys = $this->loginService->getResponseDataKeys();

        return $response->isSuccess() && !empty($response->data($dataKeys));
    }
}