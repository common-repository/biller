<?php

namespace Biller\BusinessLogic\API\Logger\Proxy;

use Biller\BusinessLogic\API\Authorization\Proxy\AuthorizedProxy;
use Biller\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use Biller\BusinessLogic\API\Logger\Request\ExportLogsRequest;
use Biller\Infrastructure\Http\Exceptions\HttpCommunicationException;
use Biller\Infrastructure\Http\Exceptions\HttpRequestException;
use Biller\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;

/**
 * Class LoggerProxy
 *
 * @package Biller\BusinessLogic\API\Logger\Proxy
 */
class LoggerProxy extends AuthorizedProxy
{
    /**
     * Send export log request
     *
     * @throws HttpRequestException
     * @throws RequestNotSuccessfulException
     * @throws QueryFilterInvalidParamException
     * @throws HttpCommunicationException
     */
    public function exportLogs(ExportLogsRequest $logsRequest)
    {
        $this->post($logsRequest);
    }
}