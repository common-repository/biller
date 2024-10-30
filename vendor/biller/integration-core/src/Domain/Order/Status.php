<?php

namespace Biller\Domain\Order;

/**
 * Class Status
 *
 * @package Biller\Domain\Order
 */
class Status
{
    const BILLER_STATUS_PENDING = 'pending';
    const BILLER_STATUS_ACCEPTED = 'accepted';
    const BILLER_STATUS_REFUNDED = 'refunded';
    const BILLER_STATUS_PARTIALLY_REFUNDED = 'partially_refunded';
    const BILLER_STATUS_CAPTURED = 'captured';
    const BILLER_STATUS_PARTIALLY_CAPTURED = 'partially_captured';
    const BILLER_STATUS_FAILED = 'failed';
    const BILLER_STATUS_REJECTED = 'rejected';
    const BILLER_STATUS_CANCELLED = 'cancelled';

    /**
     * @var string
     */
    private $status;

    /**
     * @param string $status
     */
    private function __construct($status)
    {
        $this->status = $status;
    }

    /**
     * Create status object from status code
     *
     * @param string $status
     * @return Status
     */
    public static function fromString($status)
    {
        return new self($status);
    }

    public function __toString()
    {
        return $this->status;
    }

    /**
     * Is order status accepted
     *
     * @return bool
     */
    public function isAccepted()
    {
        return $this->status === self::BILLER_STATUS_ACCEPTED;
    }

    /**
     * Is order status refunded
     *
     * @return bool
     */
    public function isRefunded()
    {
        return $this->status === self::BILLER_STATUS_REFUNDED;
    }

    /**
     * Is order status partially refunded
     *
     * @return bool
     */
    public function isRefundedPartially()
    {
        return $this->status === self::BILLER_STATUS_PARTIALLY_REFUNDED;
    }

    /**
     * Is order status captured
     *
     * @return bool
     */
    public function isCaptured()
    {
        return $this->status === self::BILLER_STATUS_CAPTURED;
    }

    /**
     * Is order status pending
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::BILLER_STATUS_PENDING;
    }

    /**
     * Is order status captured
     *
     * @return bool
     */
    public function isPartiallyCaptured()
    {
        return $this->status === self::BILLER_STATUS_PARTIALLY_CAPTURED;
    }

    /**
     * Is order status failed
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->status === self::BILLER_STATUS_FAILED;
    }

    /**
     * Is order status failed
     *
     * @return bool
     */
    public function isRejected()
    {
        return $this->status === self::BILLER_STATUS_REJECTED;
    }

    /**
     * Is order status cancelled
     *
     * @return bool
     */
    public function isCancelled()
    {
        return $this->status === self::BILLER_STATUS_CANCELLED;
    }
}