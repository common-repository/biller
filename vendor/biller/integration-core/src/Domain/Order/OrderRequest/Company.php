<?php

namespace Biller\Domain\Order\OrderRequest;


use Biller\Domain\Exceptions\InvalidArgumentException;

/**
 * Class Company
 *
 * @package Biller\Domain\Order
 */
class Company
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string|null
     */
    private $registrationNumber;
    /**
     * @var string|null
     */
    private $vatNumber;
    /**
     * @var string|null
     */
    private $website;

    /**
     * @param string $name
     * @param string|null $registrationNumber
     * @param string|null $vatNumber
     * @param string|null $website
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        $name,
        $registrationNumber = null,
        $vatNumber = null,
        $website = null
    ) {
        if(empty($name)) {
            throw new InvalidArgumentException('Buyer company name is required!');
        }
        $this->name = $name;
        $this->registrationNumber = $registrationNumber;
        $this->vatNumber = $vatNumber;
        $this->website = $website;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getRegistrationNumber()
    {
        return $this->registrationNumber;
    }

    /**
     * @return string|null
     */
    public function getVatNumber()
    {
        return $this->vatNumber;
    }

    /**
     * @return string|null
     */
    public function getWebsite()
    {
        return $this->website;
    }

}