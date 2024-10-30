<?php

namespace Biller\BusinessLogic\API\Authorization\Proxy;

use Biller\BusinessLogic\API\Http\Proxy;
use Biller\Infrastructure\Http\HttpClient;

/**
 * Class AuthorizedProxy
 *
 * @package Biller\BusinessLogic\API\Http\Authorized
 */
class AuthorizedProxy extends Proxy
{
	/**
	 * @var string
	 */
    protected $accessToken;

    /**
     * AuthorizedProxy constructor.
     *
     * @param HttpClient $client
     * @param $mode
     * @param $accessToken
     */
    public function __construct(HttpClient $client, $mode, $accessToken)
    {
        parent::__construct($client, $mode);
        $this->accessToken = $accessToken;
    }

    /**
     * Retrieves request headers.
     *
     * @return array Complete list of request headers.
     *
     */
    protected function getHeaders()
    {
        return array_merge(parent::getHeaders(), ['Authorization'=> 'Authorization: Bearer '. $this->accessToken]);
    }
}
