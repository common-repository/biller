<?php

namespace Biller\BusinessLogic\API\DTO\Refund;

use Biller\Domain\Exceptions\CurrencyMismatchException;
use Biller\Domain\Order\OrderRequest\Discount;
use Biller\Domain\Refunds\RefundLine;
use Biller\Infrastructure\Data\DataTransferObject;

class Invoice extends DataTransferObject
{
    /**
     * @var string
     */
    private $invoiceUUID;

    /**
     * @var string|null
     */
    private $externalRefundUid;

    /**
     * @var RefundLine[]
     */
    private $refundLines;

    /**
     * @var RefundAmount[]
     */
    private $refundAmounts;

    /**
     * @var Discount|null
     */
    private $discount;

    /**
     * @param string $invoiceUUID
     * @param string|null $externalRefundUid
     * @param RefundLine[] $refundLines
     * @param RefundAmount[] $refundAmounts
     * @param Discount|null $discount
     */
    public function __construct(
        $invoiceUUID,
        $externalRefundUid = null,
        array $refundLines = array(),
        array $refundAmounts = array(),
        Discount $discount = null
    ) {
        $this->invoiceUUID       = $invoiceUUID;
        $this->externalRefundUid = $externalRefundUid;
        $this->refundLines       = $refundLines;
        $this->refundAmounts     = $refundAmounts;
        $this->discount          = $discount;
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
     * @return RefundAmount[]
     */
    public function getRefundAmounts()
    {
        return $this->refundAmounts;
    }

    /**
     * @param RefundAmount[] $refundAmounts
     */
    public function setRefundAmounts(array $refundAmounts)
    {
        $this->refundAmounts = $refundAmounts;
    }

    public function addRefundAmount(RefundAmount $refundAmount)
    {
        $this->refundAmounts[] = $refundAmount;
    }

    public function hasRefundAmounts()
    {
        return !empty($this->refundAmounts);
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
     * @throws CurrencyMismatchException
     */
    public function toArray()
    {
        $array= [];
        $array['invoice_uuid'] = $this->invoiceUUID;

        if ($this->externalRefundUid !== null) {
            $array['external_refund_uid'] = $this->externalRefundUid;
        }

        if ( ! empty($this->refundLines)) {
            $array['refund_lines'] = $this->transformRefundLines();
        }

        if ( ! empty($this->refundAmounts)) {
            $array['refund_amounts'] = RefundAmount::toBatch($this->refundAmounts);
        }

        if ($this->discount !== null) {
            $array['discount'] = [
                "description" => $this->discount->getDescription(),
                "amount_incl_tax" => $this->discount->getAmount()->getAmountInclTax()->getAmount(),
                "amount_excl_tax" => $this->discount->getAmount()->getAmountExclTax()->getAmount(),
                "amount_tax" => $this->discount->getAmount()->getAmountInclTax()->minus($this->discount->getAmount()->getAmountExclTax())->getAmount()
            ];
        }

        return $array;
    }

    /**
     * @return array
     */
    private function transformRefundLines()
    {
        $refundLines = [];

        foreach ($this->refundLines as $refundLine) {
            $refundLines[] = [
                "product_id" => $refundLine->getProductId(),
                "quantity" => $refundLine->getRefundableQuantity(),
                "total_amount_excl_tax" => $refundLine->getAmount()->getAmountExclTax()->getAmount(),
                "total_amount_incl_tax" => $refundLine->getAmount()->getAmountInclTax()->getAmount(),
                "description" => ""
            ];
        }

        return $refundLines;
    }
}