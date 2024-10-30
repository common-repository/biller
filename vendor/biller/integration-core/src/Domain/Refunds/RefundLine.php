<?php

namespace Biller\Domain\Refunds;

use Biller\Domain\Amount\TaxableAmount;

/**
 * Class RefundLine
 *
 * @package Biller\Domain\Refunds
 */
class RefundLine
{
    /**
     * @var string
     */
    private $productId;
    /**
     * @var TaxableAmount
     */
    private $amount;

    /**
     * @var int|null
     */
    private $refundableQuantity;

    /**
     * @var string|null
     */
    private $invoiceUUID;

    /**
     * @param string $productId
     * @param TaxableAmount $amount
     * @param int|null $refundableQuantity
     * @param string|null $invoiceUUID
     */
    public function __construct($productId, TaxableAmount $amount, $refundableQuantity = null, $invoiceUUID= null)
    {
        $this->productId = $productId;
        $this->amount = $amount;
        $this->refundableQuantity = $refundableQuantity;
        $this->invoiceUUID = $invoiceUUID;
    }

    /**
     * @return string
     */
    public function getProductId()
    {
        return $this->productId;
    }

	/**
	 * @return TaxableAmount
	 */
	public function getAmount()
    {
		return $this->amount;
	}

    /**
     * @return int|null
     */
    public function getRefundableQuantity()
    {
        return $this->refundableQuantity;
    }

    /**
     * @return string|null
     */
    public function getInvoiceUUID()
    {
        return $this->invoiceUUID;
    }

}