<?php

namespace Biller\Domain\Order\OrderRequest;

use Biller\Domain\Amount\TaxableAmount;
use Biller\Domain\Exceptions\InvalidArgumentException;

/**
 * Class Discount
 *
 * @package Biller\Domain\Order
 */
class Discount
{
    /**
     * @var string
     */
    private $description;
    /**
     * @var TaxableAmount
     */
    private $amount;

    /**
     * @param string $description
     * @param TaxableAmount $amount
     * @throws InvalidArgumentException
     */
    public function __construct(
        $description,
        TaxableAmount $amount
    ) {
        if ($amount->getAmountInclTax()->getAmount() < 0) {
            throw new InvalidArgumentException("Discount amount including taxes must be positive number!");
        }

        if ($amount->getAmountExclTax()->getAmount() < 0) {
            throw new InvalidArgumentException("Discount amount excluding taxes must be positive number!");
        }

        $this->description = $description;
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
     * @return TaxableAmount
     */
    public function getAmount()
    {
        return $this->amount;
    }

}