<?php

namespace Biller\BusinessLogic\Authorization;

use Biller\BusinessLogic\API\Authorization\Proxy\TokenProxy;
use Biller\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use Biller\BusinessLogic\Authorization\DTO\UserInfo;
use Biller\BusinessLogic\Authorization\Exceptions\FailedToRetrieveAuthInfoException;
use Biller\BusinessLogic\Authorization\Exceptions\UnauthorizedException;
use Biller\BusinessLogic\Integration\Authorization\UserInfoRepository;
use Biller\Domain\Authorization\AuthToken;
use Biller\Domain\Exceptions\InvalidArgumentException;
use Biller\Infrastructure\Http\Exceptions\HttpCommunicationException;
use Biller\Infrastructure\Http\Exceptions\HttpRequestException;
use Biller\Infrastructure\Http\HttpClient;
use Biller\Infrastructure\Logger\Logger;
use Biller\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Biller\Infrastructure\ServiceRegister;
use Biller\Infrastructure\Singleton;
use Exception;

/**
 * Class AuthorizationService
 *
 * @package Biller\BusinessLogic\Authorization
 */
class AuthorizationService extends Singleton implements Contracts\AuthorizationService
{
	/**
	 * Singleton instance of this class.
	 *
	 * @var static
	 */
	protected static $instance;

	/**
	 * @inheritDoc
	 * @throws FailedToRetrieveAuthInfoException
	 */
	public function getUserInfo()
	{
		$userInfo = $this->getUserInfoRepository()->getActiveUserInfo();

		if ($userInfo === null) {
			throw new FailedToRetrieveAuthInfoException('Failed to retrieve auth info.');
		}

		return $userInfo;
	}

	/**
	 * @inheritdoc
	 * @throws UnauthorizedException
	 */
	public function validate($username, $password, $mode = '')
	{
		$tokenProxy = $this->createTokenProxy($mode);

		try {
			$tokenProxy->getToken($username, $password);
		} catch (Exception $e) {
			Logger::logWarning('Invalid username or password!');

			throw new UnauthorizedException('Invalid username or password!');
		}

		return true;
	}

	/**
	 *
	 * @inheritDoc
	 * @throws UnauthorizedException
	 */
	public function authorize($username, $password, $webShopUID, $mode = '' )
	{
		$tokenProxy = $this->createTokenProxy($mode);

		try {
			$authInfo = $tokenProxy->getToken($username, $password);
			$this->getUserInfoRepository()->saveUserInfo(new UserInfo(
				$mode,
				$webShopUID,
				$username,
				$password,
				$authInfo->getAccessToken(),
				$authInfo->getRefreshToken()
			));
			$this->getUserInfoRepository()->saveMode($mode);
		} catch (Exception $e) {
			Logger::logWarning('Invalid username or password!');

			throw new UnauthorizedException('Invalid username or password!');
		}
	}

	/**
	 * @inheritDoc
	 *
	 * @return AuthToken
	 * @throws FailedToRetrieveAuthInfoException
	 * @throws HttpCommunicationException
	 * @throws HttpRequestException
	 * @throws InvalidArgumentException
	 * @throws QueryFilterInvalidParamException
	 * @throws RequestNotSuccessfulException
	 * @throws UnauthorizedException
	 */
	public function getValidAccessToken()
	{
		$userInfo = $this->getUserInfo();
		if (!$userInfo->getAccessToken()->isAboutToExpire()) {
			return $userInfo->getAccessToken();
		}

		$this->refreshToken($userInfo);

		return $this->getValidAccessToken();
	}

	/**
	 * @param UserInfo $userInfo
	 * @return void
	 * @throws HttpCommunicationException
	 * @throws HttpRequestException
	 * @throws InvalidArgumentException
	 * @throws QueryFilterInvalidParamException
	 * @throws RequestNotSuccessfulException
	 * @throws UnauthorizedException
	 */
	private function refreshToken(UserInfo $userInfo)
	{
		if ($userInfo->getRefreshToken()->isAboutToExpire()) {
			$this->authorize(
				$userInfo->getUsername(),
				$userInfo->getPassword(),
				$userInfo->getWebShopUID(),
				$userInfo->getMode()
			);

			return;
		}

		$authInfo = $this->createTokenProxy($userInfo->getMode())
		                 ->refreshToken((string)$userInfo->getRefreshToken());

		$userInfo->setAccessToken($authInfo->getAccessToken());
		$userInfo->setRefreshToken($authInfo->getRefreshToken());
		$this->getUserInfoRepository()->saveUserInfo($userInfo);
	}

	/**
	 * @param string $mode
	 * @return TokenProxy
	 */
	protected function createTokenProxy($mode)
	{
		return new TokenProxy(ServiceRegister::getService(HttpClient::class), $mode);
	}

	/**
	 * Retrieves user info repository
	 *
	 * @return UserInfoRepository
	 */
	private function getUserInfoRepository()
	{
		return ServiceRegister::getService(UserInfoRepository::class);
	}
}