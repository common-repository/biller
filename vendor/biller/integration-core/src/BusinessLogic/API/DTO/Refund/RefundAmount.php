<?php

namespace Biller\BusinessLogic\API\DTO\Refund;

use Biller\Domain\Amount\Amount;
use Biller\Infrastructure\Data\DataTransferObject;

class RefundAmount extends DataTransferObject
{
    /**
     * @var string
     */
    private $taxRatePercentage;

    /**
     * @var Amount
     */
    private $totalAmountExclTax;

    /**
     * @var Amount
     */
    private $totalAmountInclTax;

    /**
     * @var string
     */
    private $description;

    /**
     * @param string $taxRatePercentage
     * @param Amount $totalAmountExclTax
     * @param Amount $totalAmountInclTax
     * @param string $description
     */
    public function __construct($taxRatePercentage, Amount $totalAmountExclTax, Amount $totalAmountInclTax, $description)
    {
        $this->taxRatePercentage = $taxRatePercentage;
        $this->totalAmountExclTax = $totalAmountExclTax;
        $this->totalAmountInclTax = $totalAmountInclTax;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getTaxRatePercentage()
    {
        return $this->taxRatePercentage;
    }

    /**
     * @param string $taxRatePercentage
     */
    public function setTaxRatePercentage($taxRatePercentage)
    {
        $this->taxRatePercentage = $taxRatePercentage;
    }

    /**
     * @return Amount
     */
    public function getTotalAmountExclTax()
    {
        return $this->totalAmountExclTax;
    }

    /**
     * @param Amount $totalAmountExclTax
     */
    public function setTotalAmountExclTax(Amount $totalAmountExclTax)
    {
        $this->totalAmountExclTax = $totalAmountExclTax;
    }

    /**
     * @return Amount
     */
    public function getTotalAmountInclTax()
    {
        return $this->totalAmountInclTax;
    }

    /**
     * @param Amount $totalAmountInclTax
     */
    public function setTotalAmountInclTax(Amount $totalAmountInclTax)
    {
        $this->totalAmountInclTax = $totalAmountInclTax;
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
            'tax_rate_percentage' => $this->taxRatePercentage,
            'total_amount_excl_tax' => $this->totalAmountExclTax->getAmount(),
            'total_amount_incl_tax' => $this->totalAmountInclTax->getAmount(),
            'description' => $this->description
        ];
    }
}