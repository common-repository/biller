<?php

namespace Biller\Domain\Order\OrderRequest;

use Biller\Domain\Exceptions\InvalidCountryCode;

/**
 * Class Country
 *
 * @package Biller\Domain\Order
 */
class Country
{
    private static $supportedCountries = ['BE', 'NL'];
    /**
     * @var string
     */
    private $country;

    /**
     * @param string $country
     */
    private function __construct($country)
    {
        $this->country = $country;
    }

    /**
     * Country iso code should be in two letters format
     *
     * @param string $isoCode
     *
     * @return Country
     *
     * @throws InvalidCountryCode
     */
    public static function fromIsoCode($isoCode)
    {
        $countryIsoCode = strtoupper($isoCode);
        if (!in_array($countryIsoCode, self::$supportedCountries, true)) {
            throw new InvalidCountryCode("The country code '$countryIsoCode' is not supported by the Biller.");
        }

        return new self($countryIsoCode);
    }

    /**
     * @return Country
     *
     * @throws InvalidCountryCode
     */
    public static function getDefault()
    {
        return self::fromIsoCode('NL');
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->country;
    }
}
