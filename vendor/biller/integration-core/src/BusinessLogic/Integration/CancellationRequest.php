<?php

namespace Biller\BusinessLogic\Integration;

class CancellationRequest
{
    /**
     * @var string
     */
    protected $shopOrderId;

    /**
     * @var bool
     */
    protected $isPartialCancellation;

    /**
     * @param string $shopOrderId
     * @param bool $isPartialCancellation
     */
    public function __construct($shopOrderId, $isPartialCancellation)
    {
        $this->shopOrderId = $shopOrderId;
        $this->isPartialCancellation = $isPartialCancellation;
    }

    /**
     * @return string
     */
    public function getShopOrderId()
    {
        return $this->shopOrderId;
    }

    /**
     * @param string $shopOrderId
     */
    public function setShopOrderId($shopOrderId)
    {
        $this->shopOrderId = $shopOrderId;
    }

    /**
     * @return bool
     */
    public function isPartialCancellation()
    {
        return $this->isPartialCancellation;
    }

    /**
     * @param bool $isPartialCancellation
     */
    public function setIsPartialCancellation($isPartialCancellation)
    {
        $this->isPartialCancellation = $isPartialCancellation;
    }
}