<?php

namespace Biller\BusinessLogic\API\Authorization\Proxy;

use Biller\BusinessLogic\API\Authorization\Response\AuthTokens;
use Biller\BusinessLogic\API\Http\Request\HttpRequest;
use Biller\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use Biller\BusinessLogic\API\Http\Proxy;
use Biller\Domain\Exceptions\InvalidArgumentException;
use Biller\Infrastructure\Http\Exceptions\HttpCommunicationException;
use Biller\Infrastructure\Http\Exceptions\HttpRequestException;
use Biller\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class TokenProxy
 *
 * @package Biller\BusinessLogic\API\Http\Token
 */
class TokenProxy extends Proxy
{
    /**
     * Fetch access and refresh token
     *
     * @param $username
     * @param $password
     * @return AuthTokens
     *
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException|InvalidArgumentException
     */
    public function getToken($username, $password)
    {
        $response = $this->post(new HttpRequest('token/', ['username' => $username, 'password' => $password]));

        return AuthTokens::fromArray(json_decode($response->getBody(), true));
    }

    /**
     * Refresh access token
     *
     * @param $refreshToken
     * @return AuthTokens
     *
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     * @throws InvalidArgumentException
     */
    public function refreshToken($refreshToken)
    {
        $response = $this->post(new HttpRequest('refresh/', ['refresh' => $refreshToken]));

        return AuthTokens::fromArray(array_merge(json_decode($response->getBody(), true), ['refresh' => $refreshToken]));
    }
}