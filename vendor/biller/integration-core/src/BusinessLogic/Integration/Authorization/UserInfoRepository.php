<?php

namespace Biller\BusinessLogic\Integration\Authorization;

use Biller\BusinessLogic\Authorization\DTO\UserInfo;

/**
 * Class UserInfoRepository
 *
 * @package Biller\BusinessLogic\Authorization\Repository
 */
interface UserInfoRepository
{
    /**
     * Save user info data
     * Some user info data are sensitive, so they must be encrypted.
     * The password must be encrypted using the encryption mechanism offered by the shop.
     *
     * @param UserInfo $userInfo
     * @return void
     */
    public function saveUserInfo(UserInfo $userInfo);


	/**
	 * Fetch user info data
	 * Some sensitive data within the user info is encrypted, it must be decrypted before user info data is returned.
	 * The password stored in the database is encrypted using the encryption mechanism offered by the shop. It must be
	 * decrypted before user info data is returned.
	 *
	 * @return UserInfo|null
	 */
	public function getActiveUserInfo();

	/**
	 * Save active mode
	 *
	 * @param string $mode
	 * @return void
	 */
	public function saveMode($mode);
}