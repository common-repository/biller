<?php

namespace Biller\BusinessLogic\Authorization\DTO;

use Biller\Domain\Authorization\AuthToken;
use Biller\Domain\Exceptions\InvalidArgumentException;
use Biller\Infrastructure\Data\DataTransferObject;

/**
 * Class UserInfo
 *
 * @package Biller\BusinessLogic\Authorization\DTO
 */
class UserInfo extends DataTransferObject
{
    /**
     * @var string
     */
    protected $mode;
    /**
     * @var string
     */
    protected $webShopUID;
    /**
     * @var string
     */
    protected $username;
    /**
     * @var string
     */
    protected $password;
    /**
     * @var AuthToken
     */
    protected $accessToken;

    /**
     * @var AuthToken
     */
    protected $refreshToken;

    /**
     * @param string $mode
     * @param string $webShopUID
     * @param string $username
     * @param string $password
     * @param AuthToken $accessToken
     * @param AuthToken $refreshToken
     */
    public function __construct(
        $mode,
        $webShopUID,
        $username,
        $password,
        AuthToken $accessToken,
        AuthToken $refreshToken
    ) {
        $this->mode = $mode;
        $this->webShopUID = $webShopUID;
        $this->username = $username;
        $this->password = $password;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
    }

    /**
     * Creates instance of UserInfo.
     *
     * @param array $data
     *
     * @return UserInfo
     * @throws InvalidArgumentException
     */
    public static function fromArray(array $data)
    {
        return new self($data['mode'], $data['webShopUID'], $data['username'], $data['password'], new AuthToken($data['accessToken']),
            new AuthToken($data['refreshToken']));
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return string
     */
    public function getWebShopUID()
    {
        return $this->webShopUID;
    }

    /**
     * @param string $webShopUID
     */
    public function setWebShopUID($webShopUID)
    {
        $this->webShopUID = $webShopUID;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
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
        return array(
            'mode' => $this->getMode(),
            'webShopUID' => $this->getWebShopUID(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'accessToken' => $this->getAccessToken()->getToken(),
            'refreshToken' => $this->getRefreshToken()->getToken()
        );
    }
}