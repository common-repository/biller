<?php

namespace Biller\Domain\Order\OrderRequest;


/**
 * Class PaymentLinkDuration
 *
 * @package Biller\Domain\Order
 */
class PaymentLinkDuration
{
    /**
     * @var int
     */
    private $duration;

    /**
     * @param int $duration
     */
    private function __construct($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return PaymentLinkDuration
     */
    public static function fifteenMinutes()
    {
        return new self(900);
    }

    /**
     * @return PaymentLinkDuration
     */
    public static function oneDay()
    {
        return new self(86400);
    }

    /**
     * @return PaymentLinkDuration
     */
    public static function oneWeek()
    {
        return new self(604800);
    }

    /**
     * @return PaymentLinkDuration
     */
    public static function getDefault()
    {
        return self::fifteenMinutes();
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }
}