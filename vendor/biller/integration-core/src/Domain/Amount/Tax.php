<?php

namespace Biller\Domain\Amount;

use Biller\Domain\Exceptions\InvalidTaxPercentage;

/**
 * Class Tax
 *
 * @package Biller\Domain\Order
 */
class Tax
{
    /**
     * @var float
     */
    private $percentage;

    /**
     * @param float $percentage
     * @throws InvalidTaxPercentage
     */
    public function __construct($percentage)
    {
        if ($percentage < 0 || $percentage >= 100) {
            throw new InvalidTaxPercentage('Tax percentage should be between 0 and 100');
        }
        $this->percentage = $percentage;
    }

    /**
     * @return float
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    public function __toString()
    {
        return number_format($this->percentage, 6);
    }

}