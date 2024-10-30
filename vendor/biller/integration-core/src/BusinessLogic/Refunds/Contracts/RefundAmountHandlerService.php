<?php

namespace Biller\BusinessLogic\Refunds\Contracts;

use Biller\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use Biller\BusinessLogic\Integration\RefundAmountRequest;
use Biller\Infrastructure\Http\Exceptions\HttpCommunicationException;
use Biller\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

interface RefundAmountHandlerService
{
    /**
     * Handles the amount refunds originated from integration. The integration should use this service to sync
     * integration amount refunds to the Biller platform.
     *
     * @param RefundAmountRequest $request
     *
     * @throws RequestNotSuccessfulException
     * @throws QueryFilterInvalidParamException
     * @throws HttpCommunicationException
     */
    public function handle(RefundAmountRequest $request);
}