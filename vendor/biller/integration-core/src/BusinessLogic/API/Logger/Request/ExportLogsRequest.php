<?php

namespace Biller\BusinessLogic\API\Logger\Request;

use Biller\BusinessLogic\API\Http\Request\HttpRequest;

/**
 * Class ExportLogsRequest
 *
 * @package Biller\BusinessLogic\API\Logger\Request
 */
class ExportLogsRequest extends HttpRequest
{
    /**
     * Create ExportLogsRequest based on OrderRequest
     *
     * @param ExportLogMessage[] $messages
     * @return ExportLogsRequest
     */
    public static function create(array $messages)
    {
//        TODO: Set endpoint and edit export log message
        return new self('', self::getLoggerRequestDTO($messages));
    }

    /**
     * @param array $messages
     *
     * @return array
     */
    private static function getLoggerRequestDTO(array $messages)
    {
        return ['messages' => ExportLogMessage::toBatchArray($messages)];
    }
}