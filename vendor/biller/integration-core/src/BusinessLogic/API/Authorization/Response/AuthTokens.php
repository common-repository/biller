<?php

namespace Biller\BusinessLogic\API\Authorization\Response;

use Biller\Domain\Authorization\AuthToken;
use Biller\Domain\Exceptions\InvalidArgumentException;
use Biller\Infrastructure\Data\DataTransferObject;

/**
 * Class AuthTokens
 *
 * @package Biller\BusinessLogic\API\Authorization\DTO
 */
class AuthTokens extends DataTransferObject
{
    /**
     * @var AuthToken
     */
    protected $accessToken;
    /**
     * @var AuthToken
     */
    protected $refreshToken;

    /**
     * Creates instance of the data transfer object from an array.
     *
     * @param array $data
     *
     * @return AuthTokens
     * @throws InvalidArgumentException
     */
    public static function fromArray(array $data)
    {
        $accountInfo = new self();

        $accountInfo->setAccessToken(new AuthToken(static::getDataValue($data, 'access')));
        $accountInfo->setRefreshToken(new AuthToken(static::getDataValue($data, 'refresh')));

        return $accountInfo;
    }

    /**
     * @return AuthToken
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param AuthToken $accessToken
     */
    public function setAccessToken(AuthToken $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return AuthToken
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param AuthToken $refreshToken
     */
    public function setRefreshToken(AuthToken $refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'access' => $this->accessToken,
            'refresh' => $this->refreshToken,
        ];
    }
}
