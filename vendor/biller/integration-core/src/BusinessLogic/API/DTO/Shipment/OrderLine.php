<?php

namespace Biller\BusinessLogic\API\DTO\Shipment;

use Biller\Infrastructure\Data\DataTransferObject;

class OrderLine extends DataTransferObject
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
     * @var int|null
     */
    private $productPriceExclTax;

    /**
     * @var int|null
     */
    private $productPriceInclTax;

    /**
     * @var string|null
     */
    private $productTaxRatePercentage;

    /**
     * @param string $productId
     * @param int $quantity
     * @param int|null $productPriceExclTax
     * @param int|null $productPriceInclTax
     * @param string|null $productTaxRatePercentage
     */
    public function __construct(
        $productId,
        $quantity,
        $productPriceExclTax = null,
        $productPriceInclTax = null,
        $productTaxRatePercentage = null
    ) {
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->productPriceExclTax = $productPriceExclTax;
        $this->productPriceInclTax = $productPriceInclTax;
        $this->productTaxRatePercentage = $productTaxRatePercentage;
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
     * @return int|null
     */
    public function getProductPriceExclTax()
    {
        return $this->productPriceExclTax;
    }

    /**
     * @param int|null $productPriceExclTax
     */
    public function setProductPriceExclTax($productPriceExclTax)
    {
        $this->productPriceExclTax = $productPriceExclTax;
    }

    /**
     * @return int|null
     */
    public function getProductPriceInclTax()
    {
        return $this->productPriceInclTax;
    }

    /**
     * @param int|null $productPriceInclTax
     */
    public function setProductPriceInclTax($productPriceInclTax)
    {
        $this->productPriceInclTax = $productPriceInclTax;
    }

    /**
     * @return string|null
     */
    public function getProductTaxRatePercentage()
    {
        return $this->productTaxRatePercentage;
    }

    /**
     * @param string|null $productTaxRatePercentage
     */
    public function setProductTaxRatePercentage($productTaxRatePercentage)
    {
        $this->productTaxRatePercentage = $productTaxRatePercentage;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = [
            'product_id' => $this->productId,
            'quantity' => $this->quantity
        ];

        if ($this->productPriceExclTax !== null) {
            $array['product_price_excl_tax'] = $this->productPriceExclTax;
        }
        if ($this->productPriceInclTax !== null) {
            $array['product_price_incl_tax'] = $this->productPriceInclTax;
        }
        if ($this->productTaxRatePercentage !== null) {
            $array['product_tax_rate_percentage'] = $this->productTaxRatePercentage;
        }

        return $array;
    }
}