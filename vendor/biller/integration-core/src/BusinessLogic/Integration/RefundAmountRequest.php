<?php

namespace Biller\BusinessLogic\Integration;

use Biller\Domain\Amount\Amount;
use Biller\Domain\Order\OrderRequest\Discount;

class RefundAmountRequest
{
    /**
     * @var string
     */
    private $shopOrderId;
    /**
     * @var string
     */
    private $description;
    /**
     * @var Amount
     */
    private $refundAmountTotal;
    /**
     * @var string|null
     */
    private $externalRefundUid;
    /**
     * @var Discount|null
     */
    private $discount;

    /**
     * RefundAmountRequest constructor.
     *
     * @param string $shopOrderId
     * @param string $description
     * @param Amount $refundAmountTotal
     * @param string|null $externalRefundUid
     * @param Discount|null $discount
     */
    public function __construct(
        $shopOrderId,
        $description,
        Amount $refundAmountTotal,
        $externalRefundUid = null,
        Discount $discount = null
    ) {
        $this->shopOrderId = $shopOrderId;
        $this->description = $description;
        $this->refundAmountTotal = $refundAmountTotal;
        $this->externalRefundUid = $externalRefundUid;
        $this->discount = $discount;
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getExternalRefundUid()
    {
        return $this->externalRefundUid;
    }

    /**
     * @param string|null $externalRefundUid
     */
    public function setExternalRefundUid($externalRefundUid)
    {
        $this->externalRefundUid = $externalRefundUid;
    }

    /**
     * @return Amount
     */
    public function getRefundAmountTotal()
    {
        return $this->refundAmountTotal;
    }

    /**
     * @param Amount $refundAmountTotal
     */
    public function setRefundAmountTotal($refundAmountTotal)
    {
        $this->refundAmountTotal = $refundAmountTotal;
    }

    /**
     * @return Discount|null
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param Discount|null $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }
}