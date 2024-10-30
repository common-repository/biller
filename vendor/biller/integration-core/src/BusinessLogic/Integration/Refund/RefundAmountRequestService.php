<?php

namespace Biller\BusinessLogic\Integration\Refund;

use Biller\BusinessLogic\Integration\RefundAmountRequest;
use Exception;

interface RefundAmountRequestService
{
    /**
     * Reject refund amount action from integration. When refund from integration is triggered if it is not possible
     * on Biller side the integration refund amount action should be rejected and if rejection is not possible it should
     * be returned as not permitted flag in the response.
     *
     * @param RefundAmountRequest $request
     * @param Exception $reason
     * @return RefundAmountRejectResponse
     */
    public function reject(RefundAmountRequest $request, Exception $reason);
}