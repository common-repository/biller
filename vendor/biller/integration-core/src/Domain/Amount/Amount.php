<?php

namespace Biller\Domain\Amount;

use Biller\Domain\Exceptions\CurrencyMismatchException;

/**
 * Class Amount
 *
 * @package Biller\Domain\Amount
 */
class Amount
{
    /** @var int */
    private $amount;

    /** @var Currency */
    private $currency;

    /**
     * @param int $amount
     * @param Currency $currency
     */
    private function __construct($amount, Currency $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * Instantiate amount object from float value
     *
     * @param float $amount
     * @param Currency $currency
     * @return static
     */
    public static function fromFloat($amount, Currency $currency)
    {
        return new self(
            (int)round($amount * (10 ** $currency->getMinorUnits())),
            $currency
        );
    }

    /**
     * Instantiate amount object from smallest units (integer)
     *
     * @param int $amount
     * @param Currency $currency
     * @return static
     */
    public static function fromInteger($amount, Currency $currency)
    {
        return new self($amount, $currency);
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return float
     */
    public function getPriceInCurrencyUnits()
    {
        return $this->amount / (10 ** $this->currency->getMinorUnits());
    }

    /**
     * Get amount
     *
     * @throws CurrencyMismatchException
     */
    public function minus(Amount $amount)
    {
        if (!$this->getCurrency()->equal($amount->getCurrency())) {
            throw new CurrencyMismatchException();
        }
        return new self($this->getAmount() - $amount->getAmount(), $this->getCurrency());
    }

    /**
     * Get amount
     *
     * @throws CurrencyMismatchException
     */
    public function plus(Amount $amount)
    {
        if (!$this->getCurrency()->equal($amount->getCurrency())) {
            throw new CurrencyMismatchException();
        }

        return new self($this->getAmount() + $amount->getAmount(), $this->getCurrency());
    }

}