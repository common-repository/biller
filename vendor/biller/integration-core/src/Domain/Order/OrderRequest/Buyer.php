<?php

namespace Biller\Domain\Order\OrderRequest;


use Biller\Domain\Exceptions\InvalidArgumentException;

/**
 * Class Buyer
 *
 * @package Biller\Domain\Order
 */
class Buyer
{
    /**
     * @var string
     */
    private $firstName;
    /**
     * @var string
     */
    private $lastName;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string|null
     */
    private $phoneNumber;

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string|null $phoneNumber
     *
     * @throws InvalidArgumentException
     */
    public function __construct($firstName, $lastName, $email, $phoneNumber = null)
    {
        if(empty($firstName) || empty($lastName) || empty($email)) {
            throw new InvalidArgumentException('Buyer must have name and email!');
        }

        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

}