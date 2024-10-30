<?php

namespace Biller\BusinessLogic\API\DTO\Refund;

use Biller\Domain\Amount\Amount;
use Biller\Domain\Amount\Currency;
use Biller\Domain\Exceptions\InvalidCurrencyCode;
use Biller\Infrastructure\Data\DataTransferObject;

/**
 * Class RefundLine
 *
 * @package Biller\Domain\Refunds
 */
class RefundLine extends DataTransferObject
{
    /**
     * @var string
     */
    private $productId;
    /**
     * @var Amount
     */
    private $refundedAmountExclTax;
    /**
     * @var Amount
     */
    private $refundedAmountIncTax;
    /**
     * @var int
     */
    private $refundedQuantity;
    /**
     * @var string
     */
    private $description;

    /**
     * @param string $productId
     * @param Amount $refundedAmountExclTax
     * @param Amount $refundedAmountIncTax
     * @param int $refundedQuantity
     * @param string $description
     */
    public function __construct($productId, Amount $refundedAmountExclTax, Amount $refundedAmountIncTax, $refundedQuantity, $description)
    {
        $this->productId = $productId;
        $this->refundedAmountExclTax = $refundedAmountExclTax;
        $this->refundedAmountIncTax = $refundedAmountIncTax;
        $this->refundedQuantity = $refundedQuantity;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return Amount
     */
    public function getRefundedAmountExclTax()
    {
        return $this->refundedAmountExclTax;
    }

    /**
     * @return Amount
     */
    public function getRefundedAmountIncTax()
    {
        return $this->refundedAmountIncTax;
    }

    /**
     * @return int
     */
    public function getRefundedQuantity()
    {
        return $this->refundedQuantity;
    }

    /**
     * @param int $refundedQuantity
     */
    public function setRefundedQuantity($refundedQuantity)
    {
        $this->refundedQuantity = $refundedQuantity;
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

    public function toArray()
    {
        return [
            'product_id' => $this->getProductId(),
            'quantity' => $this->refundedQuantity,
            'total_amount_excl_tax' => $this->refundedAmountExclTax->getAmount(),
            'total_amount_incl_tax' => $this->refundedAmountIncTax->getAmount(),
            'description' => $this->description
        ];
    }

    /**
     * @throws InvalidCurrencyCode
     */
    public static function fromArray(array $data)
    {
        return new self(
            self::getDataValue($data, 'product_id', ''),
            Amount::fromFloat(
                self::getDataValue($data, 'total_amount_excl_tax', 0),
                Currency::fromIsoCode(self::getDataValue($data, 'currency', 'EUR'))
            ),
            Amount::fromFloat(
                self::getDataValue($data, 'total_amount_incl_tax', 0),
                Currency::fromIsoCode(self::getDataValue($data, 'currency', 'EUR'))
            ),
            self::getDataValue($data, 'quantity', 0),
            self::getDataValue($data, 'description', '')
        );
    }
}