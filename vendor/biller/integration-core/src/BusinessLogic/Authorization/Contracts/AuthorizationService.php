<?php

namespace Biller\BusinessLogic\Authorization\Contracts;

use Biller\BusinessLogic\Authorization\DTO\UserInfo;
use Biller\Domain\Authorization\AuthToken;

/**
 * Interface AuthorizationService
 *
 * @package Biller\BusinessLogic\Authorization\Contracts
 */
interface AuthorizationService
{

    /**
     * Return user info
     *
     * @return UserInfo
     */
    public function getUserInfo();

	/**
	 * @param string $username
	 * @param string $password
	 * @param string $mode
	 * @return mixed
	 */
	public function validate($username, $password, $mode = '');

    /**
     * Validate data and save user credentials
     *
     * @param string $username
     * @param string $password
     * @param string $webShopUID
     * @param string $mode
     * @return void
     */
    public function authorize($username, $password, $webShopUID, $mode = '');

    /**
     * Get valid access token
     *
     * @return AuthToken
     */
    public function getValidAccessToken();
}