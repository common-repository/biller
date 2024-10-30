<?php

namespace Biller\Domain\Order\OrderRequest;


use Biller\Domain\Exceptions\InvalidArgumentException;

/**
 * Class Address
 *
 * @package Biller\Domain\Order
 */
class Address
{
    /**
     * @var string|null
     */
    private $primaryAddress;
    /**
     * @var string|null
     */
    private $secondaryAddress;
    /**
     * @var string
     */
    private $city;
    /**
     * @var string|null
     */
    private $region;
    /**
     * @var string
     */
    private $postalCode;
    /**
     * @var Country
     */
    private $country;

    /**
     * @param string $city
     * @param string $postalCode
     * @param Country $country
     * @param string|null $primaryAddress
     * @param string|null $secondaryAddress
     * @param string|null $region
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        $city,
        $postalCode,
        Country $country,
        $primaryAddress = null,
        $secondaryAddress = null,
        $region = null
    ) {
        if(empty($postalCode) || empty($city)) {
            throw new InvalidArgumentException('Address must have city and postal code!');
        }

        $this->city = $city;
        $this->postalCode = $postalCode;
        $this->country = $country;
        $this->primaryAddress = $primaryAddress;
        $this->secondaryAddress = $secondaryAddress;
        $this->region = $region;
    }

    /**
     * @return string|null
     */
    public function getPrimaryAddress()
    {
        return $this->primaryAddress;
    }

    /**
     * @return string|null
     */
    public function getSecondaryAddress()
    {
        return $this->secondaryAddress;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string|null
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

}