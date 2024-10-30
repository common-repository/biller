<?php

namespace Biller\Domain\Authorization;

use Biller\Domain\Exceptions\InvalidArgumentException;

/**
 * Class AuthToken
 *
 * @package Biller\Domain\Authorization
 */
class AuthToken
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var int
     */
    private $expirationTime;

    /**
     * @param string $token
     * @throws InvalidArgumentException
     */
    public function __construct($token)
    {
        $tokenParts = explode(".", $token);
        if (count($tokenParts) !== 3) {
            throw new InvalidArgumentException('Invalid jwt format!');
        }
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtPayload = json_decode($tokenPayload, true);
        if (!array_key_exists('exp', $jwtPayload)) {
            throw new InvalidArgumentException('Invalid jwt format!');
        }

        $this->token = $token;
        $this->expirationTime = $jwtPayload['exp'];
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function isAboutToExpire()
    {
        return time() > $this->expirationTime - 5;
    }

    public function __toString()
    {
        return $this->token;
    }
}