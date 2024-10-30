<?php

namespace Biller\BusinessLogic\Integration;

use Biller\Domain\Amount\Amount;
use Biller\Domain\Order\OrderRequest\Discount;
use Biller\Domain\Order\OrderRequest\OrderLine;

class ShipmentRequest
{
    /**
     * @var string
     */
    protected $shopOrderId;

    /**
     * @var string|null
     */
    private $externalInvoiceUid;

    /**
     * @var Discount|null
     */
    private $discount;

    /**
     * @var Amount|null
     */
    private $totalAmount;

    /**
     * @var OrderLine[]
     */
    private $orderLines;

    /**
     * @param string $shopOrderId
     * @param string|null $externalInvoiceUid
     * @param Discount|null $discount
     * @param Amount|null $totalAmount
     * @param OrderLine[] $orderLines
     */
    public function __construct(
        $shopOrderId,
        $externalInvoiceUid = null,
        Discount $discount = null,
        Amount $totalAmount = null,
        array $orderLines = array()
    )
    {
        $this->shopOrderId = $shopOrderId;
        $this->externalInvoiceUid = $externalInvoiceUid;
        $this->discount = $discount;
        $this->totalAmount = $totalAmount;
        $this->orderLines = $orderLines;
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
     * @return string|null
     */
    public function getExternalInvoiceUid()
    {
        return $this->externalInvoiceUid;
    }

    /**
     * @param string|null $externalInvoiceUid
     */
    public function setExternalInvoiceUid($externalInvoiceUid)
    {
        $this->externalInvoiceUid = $externalInvoiceUid;
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
    public function setDiscount(Discount $discount = null)
    {
        $this->discount = $discount;
    }

    /**
     * @return Amount|null
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * @param Amount|null $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return OrderLine[]
     */
    public function getOrderLines()
    {
        return $this->orderLines;
    }

    /**
     * @param OrderLine[] $orderLines
     */
    public function setOrderLines(array $orderLines)
    {
        $this->orderLines = $orderLines;
    }
}