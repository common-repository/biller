<?php

namespace Biller\BusinessLogic\Integration\Refund;

use Biller\Domain\Refunds\RefundCollection;

/**
 * Interface OrderRefundService
 *
 * @package Biller\BusinessLogic\Refunds\Contracts
 */
interface OrderRefundService
{
    /**
     * Refund order in shop system
     *
     * @param string $externalExternalOrderUUID
     * @param RefundCollection|null $billerRefunds
     * @return void
     */
    public function refund($externalExternalOrderUUID, RefundCollection $billerRefunds = null);
}