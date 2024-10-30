<?php

namespace Biller\Domain\Order\OrderRequest;

use Biller\Domain\Amount\Tax;
use Biller\Domain\Amount\TaxableAmount;
use Biller\Domain\Exceptions\InvalidTaxPercentage;

/**
 * Class OrderLine
 *
 * @package Biller\Domain\Order
 */
class OrderLine
{
    /**
     * @var string
     */
    private $productId;
    /**
     * @var int
     */
    private $quantity;
    /**
     * @var string
     */
    private $productName;
    /**
     * @var string|null
     */
    private $productDescription;
    /**
     * @var TaxableAmount
     */
    private $taxableAmount;
    /**
     * @var string
     */
    private $taxRate;

    /**
     * @param string $productId
     * @param string $productName
     * @param TaxableAmount $taxableAmount
     * @param string $taxRate String representation of applied tax rate on order line
     * @param int $quantity
     * @param string|null $productDescription
     */
    public function __construct(
        $productId,
        $productName,
        TaxableAmount $taxableAmount,
        $taxRate,
        $quantity = 1,
        $productDescription = null
    ) {
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->productName = $productName;
        $this->productDescription = $productDescription;
        $this->taxableAmount = $taxableAmount;
        $this->taxRate = $taxRate;
    }

    /**
     * @return string
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * @return string|null
     */
    public function getProductDescription()
    {
        return $this->productDescription;
    }

    /**
     * @return TaxableAmount
     */
    public function getTaxableAmount()
    {
        return $this->taxableAmount;
    }

    /**
     * @return string
     * @throws InvalidTaxPercentage
     */
    public function getTaxRate()
    {
        $tax = new Tax((float)$this->taxRate);
        return (string)$tax;
    }

}