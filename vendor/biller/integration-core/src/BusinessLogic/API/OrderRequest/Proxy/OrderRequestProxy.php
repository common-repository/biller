<?php

namespace Biller\BusinessLogic\API\OrderRequest\Proxy;

use Biller\BusinessLogic\API\Authorization\Proxy\AuthorizedProxy;
use Biller\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use Biller\BusinessLogic\API\OrderRequest\Request\HttpOrderRequest;
use Biller\BusinessLogic\API\OrderRequest\Response\OrderRequestResponse;
use Biller\Infrastructure\Http\Exceptions\HttpCommunicationException;
use Biller\Infrastructure\Http\Exceptions\HttpRequestException;
use Biller\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class OrderRequestProxy
 *
 * @package Biller\BusinessLogic\OrderRequest
 */
class OrderRequestProxy extends AuthorizedProxy
{
    /**
     * Create order request
     *
     * @throws HttpRequestException
     * @throws RequestNotSuccessfulException
     * @throws QueryFilterInvalidParamException
     * @throws HttpCommunicationException
     */
    public function createOrderRequest(HttpOrderRequest $request)
    {
        return OrderRequestResponse::fromArray(json_decode($this->post($request)->getBody(), true));
    }
}