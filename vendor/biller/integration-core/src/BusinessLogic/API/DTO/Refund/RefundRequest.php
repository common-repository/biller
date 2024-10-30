<?php

namespace Biller\BusinessLogic\API\DTO\Refund;

use Biller\Infrastructure\Data\DataTransferObject;

class RefundRequest extends DataTransferObject
{
    /**
     * @var int
     */
    private $amount;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string|null
     */
    private $externalRefundUid;

    /**
     * @var Invoice[]
     */
    private $invoices;

    /**
     * @param int $amount
     * @param string $description
     * @param string|null $externalRefundUid
     * @param array $invoices
     */
    public function __construct($amount, $description, $externalRefundUid = null, array $invoices = array())
    {
        $this->amount = $amount;
        $this->description = $description;
        $this->externalRefundUid = $externalRefundUid;
        $this->invoices = $invoices;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
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
     * @return Invoice[]
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * @param Invoice[] $invoices
     */
    public function setInvoices(array $invoices)
    {
        $this->invoices = $invoices;
    }

    public function addInvoice(Invoice $invoice)
    {
        $this->invoices[] = $invoice;
    }

    public function toArray()
    {
        $array = [];
        $array['amount'] = $this->amount;
        $array['description'] = $this->description;

        if ($this->externalRefundUid !== null) {
            $array['external_refund_uid'] = $this->externalRefundUid;
        }

        if (!empty($this->invoices)) {
            $array['invoices'] = Invoice::toBatch($this->invoices);
        }

        return $array;
    }

    public static function fromArray(array $data)
    {
        return new self(
            self::getDataValue($data, 'amount', 0),
            self::getDataValue($data, 'description', ''),
            self::getDataValue($data, 'external_refund_uid', null),
            Invoice::fromBatch(self::getDataValue($data, 'invoices', []))
        );

    }
}