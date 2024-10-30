<?php

namespace Biller\BusinessLogic\API\DTO\Shipment;

use Biller\Domain\Order\OrderRequest\Discount;
use Biller\Infrastructure\Data\DataTransferObject;

class ShipmentRequest extends DataTransferObject
{
    /**
     * @var string|null
     */
    private $externalInvoiceUid;

    /**
     * @var Discount|null
     */
    private $discount;

    /**
     * @var int|null
     */
    private $amount;

    /**
     * @var OrderLine[]
     */
    private $orderLines;

    /**
     * @param string|null $externalInvoiceUid
     * @param Discount|null $discount
     * @param int|null $amount
     * @param OrderLine[] $orderLines
     */
    public function __construct(
        $externalInvoiceUid = null,
        Discount $discount = null,
        $amount = null,
        array $orderLines = array()
    )
    {
        $this->externalInvoiceUid = $externalInvoiceUid;
        $this->discount = $discount;
        $this->amount = $amount;
        $this->orderLines = $orderLines;
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
     * @return int|null
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int|null $amount
     */
    public function setAmount($amount = null)
    {
        $this->amount = $amount;
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

    /**
     * @return array
     */
    public function toArray()
    {
        $array = [];
        if ($this->externalInvoiceUid !== null) {
            $array['external_invoice_uid'] = $this->externalInvoiceUid;
        }
        if ($this->amount !== null) {
            $array['amount'] = $this->amount;
        }
        if ($this->discount !== null) {
            $array['discount'] = [
                'description' => $this->discount->getDescription(),
                'amount_incl_tax' => $this->discount->getAmount()->getAmountInclTax()->getAmount(),
                'amount_excl_tax' => $this->discount->getAmount()->getAmountExclTax()->getAmount(),
                'amount_tax' => $this->discount->getAmount()->getTaxAmount(),
            ];
        }
        if (!empty($this->orderLines)) {
            $array['order_lines'] = OrderLine::toBatch($this->orderLines);
        }

        return $array;
    }
}