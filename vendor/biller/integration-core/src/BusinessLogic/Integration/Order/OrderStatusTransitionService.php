<?php

namespace Biller\BusinessLogic\Integration\Order;

use Biller\BusinessLogic\API\DTO\Response\RejectResponse;
use Biller\Domain\Order\Status;

/**
 * Interface OrderStatusTransitionService
 *
 * @package Biller\BusinessLogic\Order\Contracts
 */
interface OrderStatusTransitionService
{
    /**
     * Update order status in shop system
     *
     * @param string $orderUUID
     * @param Status $status
     * @return void
     */
    public function updateStatus($orderUUID, Status $status);

    /**
     * Reject refund action
     *
     * @param string $shopOrderId
     * @param RejectResponse $response
     * @return bool
     */
    public function rejectRefund($shopOrderId, RejectResponse $response);
}