<?php

namespace Biller\Infrastructure\Logger\Interfaces;

use Biller\Infrastructure\Logger\LogData;

/**
 * Interface LoggerAdapter.
 *
 * @package Biller\Infrastructure\Logger\Interfaces
 */
interface LoggerAdapter
{
    /**
     * Log message in system
     *
     * @param LogData $data
     */
    public function logMessage(LogData $data);
}
