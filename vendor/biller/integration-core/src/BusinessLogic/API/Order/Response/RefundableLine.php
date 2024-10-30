<?php

namespace Biller\BusinessLogic\API\Order\Response;

use Biller\Infrastructure\Data\DataTransferObject;

/**
 * Class RefundableLine
 *
 * @package Biller\BusinessLogic\API\DTO\Refund
 */
class RefundableLine extends DataTransferObject
{
    /**
     * @var string
     */
    private $currency;
    /**
     * @var string
     */
    private $symbol;
    /**
     * @var string
     */
    private $productId;
    /**
     * @var string
     */
    private $productName;
    /**
     * @var float
     */
    private $productRateTaxPercentage;
    /**
     * @var int
     */
    private $quantity;
    /**
     * @var int
     */
    private $productPriceExclTax;
    /**
     * @var int
     */
    private $productPriceInclTax;
    /**
     * @var int|float
     */
    private $productPriceExclTaxInCurrencyUnits;
    /**
     * @var int|float
     */
    private $productPriceInclTaxInCurrencyUnits;
    /**
     * @var int
     */
    private $refundableQuantity;
    /**
     * @var int
     */
    private $totalRefundableAmountExclTax;
    /**
     * @var int
     */
    private $totalRefundableAmountInclTax;
    /**
     * @var int|float
     */
    private $totalRefundableAmountExclTaxInCurrencyUnits;
    /**
     * @var int|float
     */
    private $totalRefundableAmountInclTaxInCurrencyUnits;

    /**
     * @var string
     */
    private $invoiceUUID;

    public static function fromArray(array $data)
    {
        $line = new self();
        $line->setCurrency(static::getDataValue($data, 'currency'));
        $line->setSymbol(static::getDataValue($data, 'symbol'));
        $line->setProductId(static::getDataValue($data, 'product_id'));
        $line->setProductName(static::getDataValue($data, 'product_name'));
        $line->setProductRateTaxPercentage(static::getDataValue($data, 'product_tax_rate_percentage', 0));
        $line->setQuantity(static::getDataValue($data, 'quantity', 0));
        $line->setProductPriceExclTax(static::getDataValue($data, 'product_price_excl_tax', 0));
        $line->setProductPriceInclTax(static::getDataValue($data, 'product_price_incl_tax', 0));
        $line->setProductPriceExclTaxInCurrencyUnits(static::getDataValue($data,
            'product_price_excl_tax_in_currency_units', 0));
        $line->setProductPriceInclTaxInCurrencyUnits(static::getDataValue($data,
            'product_price_incl_tax_in_currency_units', 0));
        $line->setRefundableQuantity(static::getDataValue($data, 'refundable_quantity', 0));
        $line->setTotalRefundableAmountExclTax(static::getDataValue($data, 'total_refundable_amount_excl_tax', 0));
        $line->setTotalRefundableAmountInclTax(static::getDataValue($data, 'total_refundable_amount_incl_tax', 0));
        $line->setTotalRefundableAmountExclTaxInCurrencyUnits(static::getDataValue($data,
            'total_refundable_amount_excl_tax_in_currency_units', 0));
        $line->setTotalRefundableAmountInclTaxInCurrencyUnits(static::getDataValue($data,
            'total_refundable_amount_incl_tax_in_currency_units', 0));
        $line->setInvoiceUUID(static::getDataValue($data, 'uuid'));

        return $line;
    }

    public function toArray()
    {
        return [
            "currency" => $this->getCurrency(),
            "symbol" => $this->getSymbol(),
            "product_id" => $this->getProductId(),
            "product_name" => $this->getProductName(),
            "product_tax_rate_percentage" => $this->getProductRateTaxPercentage(),
            "quantity" => $this->getQuantity(),
            "product_price_excl_tax" => $this->getProductPriceExclTax(),
            "product_price_incl_tax" => $this->getProductPriceInclTax(),
            "product_price_excl_tax_in_currency_units" => $this->getProductPriceExclTaxInCurrencyUnits(),
            "product_price_incl_tax_in_currency_units" => $this->getProductPriceInclTaxInCurrencyUnits(),
            "refundable_quantity" => $this->getRefundableQuantity(),
            "total_refundable_amount_excl_tax" => $this->getTotalRefundableAmountExclTax(),
            "total_refundable_amount_incl_tax" => $this->getTotalRefundableAmountInclTax(),
            "total_refundable_amount_excl_tax_in_currency_units" => $this->getTotalRefundableAmountExclTaxInCurrencyUnits(),
            "total_refundable_amount_incl_tax_in_currency_units" => $this->getTotalRefundableAmountInclTaxInCurrencyUnits()
        ];
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @param string $symbol
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
    }

    /**
     * @return string
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param string $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * @param string $productName
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;
    }

    /**
     * @return float
     */
    public function getProductRateTaxPercentage()
    {
        return $this->productRateTaxPercentage;
    }

    /**
     * @param float $productRateTaxPercentage
     */
    public function setProductRateTaxPercentage($productRateTaxPercentage)
    {
        $this->productRateTaxPercentage = $productRateTaxPercentage;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getProductPriceExclTax()
    {
        return $this->productPriceExclTax;
    }

    /**
     * @param int $productPriceExclTax
     */
    public function setProductPriceExclTax($productPriceExclTax)
    {
        $this->productPriceExclTax = $productPriceExclTax;
    }

    /**
     * @return int
     */
    public function getProductPriceInclTax()
    {
        return $this->productPriceInclTax;
    }

    /**
     * @param int $productPriceInclTax
     */
    public function setProductPriceInclTax($productPriceInclTax)
    {
        $this->productPriceInclTax = $productPriceInclTax;
    }

    /**
     * @return float|int
     */
    public function getProductPriceExclTaxInCurrencyUnits()
    {
        return $this->productPriceExclTaxInCurrencyUnits;
    }

    /**
     * @param float|int $productPriceExclTaxInCurrencyUnits
     */
    public function setProductPriceExclTaxInCurrencyUnits($productPriceExclTaxInCurrencyUnits)
    {
        $this->productPriceExclTaxInCurrencyUnits = $productPriceExclTaxInCurrencyUnits;
    }

    /**
     * @return float|int
     */
    public function getProductPriceInclTaxInCurrencyUnits()
    {
        return $this->productPriceInclTaxInCurrencyUnits;
    }

    /**
     * @param float|int $productPriceInclTaxInCurrencyUnits
     */
    public function setProductPriceInclTaxInCurrencyUnits($productPriceInclTaxInCurrencyUnits)
    {
        $this->productPriceInclTaxInCurrencyUnits = $productPriceInclTaxInCurrencyUnits;
    }

    /**
     * @return int
     */
    public function getRefundableQuantity()
    {
        return $this->refundableQuantity;
    }

    /**
     * @param int $refundableQuantity
     */
    public function setRefundableQuantity($refundableQuantity)
    {
        $this->refundableQuantity = $refundableQuantity;
    }

    /**
     * @return int
     */
    public function getTotalRefundableAmountExclTax()
    {
        return $this->totalRefundableAmountExclTax;
    }

    /**
     * @param int $totalRefundableAmountExclTax
     */
    public function setTotalRefundableAmountExclTax($totalRefundableAmountExclTax)
    {
        $this->totalRefundableAmountExclTax = $totalRefundableAmountExclTax;
    }

    /**
     * @return int
     */
    public function getTotalRefundableAmountInclTax()
    {
        return $this->totalRefundableAmountInclTax;
    }

    /**
     * @param int $totalRefundableAmountInclTax
     */
    public function setTotalRefundableAmountInclTax($totalRefundableAmountInclTax)
    {
        $this->totalRefundableAmountInclTax = $totalRefundableAmountInclTax;
    }

    /**
     * @return float|int
     */
    public function getTotalRefundableAmountExclTaxInCurrencyUnits()
    {
        return $this->totalRefundableAmountExclTaxInCurrencyUnits;
    }

    /**
     * @param float|int $totalRefundableAmountExclTaxInCurrencyUnits
     */
    public function setTotalRefundableAmountExclTaxInCurrencyUnits($totalRefundableAmountExclTaxInCurrencyUnits)
    {
        $this->totalRefundableAmountExclTaxInCurrencyUnits = $totalRefundableAmountExclTaxInCurrencyUnits;
    }

    /**
     * @return float|int
     */
    public function getTotalRefundableAmountInclTaxInCurrencyUnits()
    {
        return $this->totalRefundableAmountInclTaxInCurrencyUnits;
    }

    /**
     * @param float|int $totalRefundableAmountInclTaxInCurrencyUnits
     */
    public function setTotalRefundableAmountInclTaxInCurrencyUnits($totalRefundableAmountInclTaxInCurrencyUnits)
    {
        $this->totalRefundableAmountInclTaxInCurrencyUnits = $totalRefundableAmountInclTaxInCurrencyUnits;
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
}