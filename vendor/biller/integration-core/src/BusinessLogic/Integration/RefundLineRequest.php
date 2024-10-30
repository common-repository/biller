<?php

namespace Biller\BusinessLogic\Integration;

use Biller\Domain\Order\OrderRequest\Discount;
use Biller\Domain\Refunds\RefundLine;

class RefundLineRequest
{
    /**
     * @var string
     */
    private $shopOrderId;

    /**
     * @var RefundLine[]
     */
    private $refundLines;

    /**
     * @var string|null
     */
    private $externalRefundUid;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $invoiceUUID;

    /**
     * @var ?Discount
     */
    private $discount;

    /**
     * @param string $shopOrderId
     * @param RefundLine[] $refundLines
     * @param string|null $externalRefundUid
     * @param string $description
     * @param string $invoiceUUID
     * @param Discount|null $discount
     */
    public function __construct(
        $shopOrderId,
        array $refundLines,
        $externalRefundUid,
        $description,
        $invoiceUUID,
        Discount $discount = null
    )
    {
        $this->shopOrderId = $shopOrderId;
        $this->refundLines = $refundLines;
        $this->externalRefundUid = $externalRefundUid;
        $this->description = $description;
        $this->invoiceUUID = $invoiceUUID;
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
     * @return RefundLine[]
     */
    public function getRefundLines()
    {
        return $this->refundLines;
    }

    /**
     * @param RefundLine[] $refundLines
     */
    public function setRefundLines(array $refundLines)
    {
        $this->refundLines = $refundLines;
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
    public function setExternalRefundUid($externalRefundUid = null)
    {
        $this->externalRefundUid = $externalRefundUid;
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
     * @return string
     */
    public function getInvoiceUUID()
    {
        return $this->invoiceUUID;
    }

    /**
     * @param string $invoiceUUID
     */
    public function setInvoiceUUID($invoiceUUID)
    {
        $this->invoiceUUID = $invoiceUUID;
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
}