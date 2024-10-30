<?php

namespace Biller\Domain\Amount;

use Biller\Domain\Exceptions\CurrencyMismatchException;
use Biller\Domain\Exceptions\InvalidTaxPercentage;

class TaxableAmount
{
    /**
     * @var Amount
     */
    private $amountExclTax;
    /**
     * @var Amount
     */
    private $amountInclTax;
    /**
     * @var Tax
     */
    private $tax;

    /**
     * @param Amount $amountExclTax
     * @param Amount $amountInclTax
     * @param Tax $tax
     */
    public function __construct(Amount $amountExclTax, Amount $amountInclTax, Tax $tax)
    {
        $this->amountExclTax = $amountExclTax;
        $this->amountInclTax = $amountInclTax;
        $this->tax = $tax;
    }

    /**
     * @param Amount $amountExlTax
     * @param Amount $amountInclTax
     *
     * @return TaxableAmount
     *
     * @throws InvalidTaxPercentage
     */
    public static function fromAmounts(Amount $amountExlTax, Amount $amountInclTax)
    {
        $tax = $amountInclTax->getAmount() > 0 || $amountExlTax->getAmount() > 0 ?
            new Tax(($amountInclTax->getAmount() / $amountExlTax->getAmount() - 1) * 100) : new Tax(0);

        return new self(
            $amountExlTax,
            $amountInclTax,
            $tax
        );
    }

    /**
     * @param Amount $amountExlTax
     * @param Tax $tax
     *
     * @return TaxableAmount
     */
    public static function fromAmountExclTaxAndTax(Amount $amountExlTax, Tax $tax)
    {
        return new self(
            $amountExlTax,
            Amount::fromFloat($amountExlTax->getPriceInCurrencyUnits() * (1 + $tax->getPercentage() / 100),
                $amountExlTax->getCurrency()),
            $tax
        );
    }

    /**
     * @param Amount $amountInclTax
     * @param Tax $tax
     *
     * @return TaxableAmount
     */
    public static function fromAmountInclTaxAndTax(Amount $amountInclTax, Tax $tax)
    {
        return new self(
            Amount::fromFloat($amountInclTax->getPriceInCurrencyUnits() / (1 + $tax->getPercentage() / 100),
                $amountInclTax->getCurrency()),
            $amountInclTax,
            $tax);
    }

    /**
     * @param Amount $amountExlTax
     *
     * @return TaxableAmount
     *
     * @throws InvalidTaxPercentage
     */
    public static function fromAmountExclTax(Amount $amountExlTax)
    {
        return new self($amountExlTax, $amountExlTax, new Tax(0));
    }

    /**
     * @param Amount $amountInclTax
     *
     * @return TaxableAmount
     *
     * @throws InvalidTaxPercentage
     */
    public static function fromAmountInclTax(Amount $amountInclTax)
    {
        return new self($amountInclTax, $amountInclTax, new Tax(0));
    }

    /**
     * @return Amount
     */
    public function getAmountExclTax()
    {
        return $this->amountExclTax;
    }

    /**
     * @return Amount
     */
    public function getAmountInclTax()
    {
        return $this->amountInclTax;
    }

    /**
     * @return Tax
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @return int
     *
     * @throws CurrencyMismatchException
     */
    public function getTaxAmount()
    {
        return $this->amountInclTax->minus($this->amountExclTax)->getAmount();
    }

    /**
     * Price without tax reduced by the price of the input parameter without tax
     * Price with tax reduced by the price of the input parameter with tax
     *
     * @return TaxableAmount
     *
     * @throws CurrencyMismatchException
     * @throws InvalidTaxPercentage
     */
    public function minus(TaxableAmount $amount)
    {
        return self::fromAmounts($this->amountExclTax->minus($amount->getAmountExclTax()),
            $this->amountInclTax->minus($amount->getAmountInclTax()));
    }

    /**
     * Price without tax increased by the price of the input parameter without tax
     * Price with tax increased by the price of the input parameter with tax
     *
     * @return TaxableAmount
     *
     * @throws CurrencyMismatchException
     * @throws InvalidTaxPercentage
     */
    public function plus(TaxableAmount $amount)
    {
        return self::fromAmounts($this->amountExclTax->plus($amount->getAmountExclTax()),
            $this->amountInclTax->plus($amount->getAmountInclTax()));
    }
}