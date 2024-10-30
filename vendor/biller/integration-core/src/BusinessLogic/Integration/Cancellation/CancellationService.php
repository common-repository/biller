<?php

namespace Biller\BusinessLogic\Integration\Cancellation;

use Biller\BusinessLogic\Integration\CancellationItem;
use Biller\BusinessLogic\Integration\RejectResponse;
use Exception;

interface CancellationService
{
    /**
     *  Reject cancellation action
     *
     * @param $request
     * @param Exception $reason
     * @return RejectResponse
     */
    public function reject($request, Exception $reason);

    /**
     * Get all order lines
     *
     * @param string $shopOrderId
     * @return CancellationItem[]
     */
    public function getAllItems($shopOrderId);
}