<?php

namespace Biller\BusinessLogic\Logger;

use Biller\BusinessLogic\API\Logger\Proxy\LoggerProxy;
use Biller\BusinessLogic\API\Logger\Request\ExportLogMessage;
use Biller\BusinessLogic\API\Logger\Request\ExportLogsRequest;
use Biller\Infrastructure\Configuration\Configuration;
use Biller\Infrastructure\Logger\Interfaces\DefaultLoggerAdapter;
use Biller\Infrastructure\Logger\LogData;
use Biller\Infrastructure\Logger\Logger;
use Biller\Infrastructure\ServiceRegister;
use Exception;

/**
 * Interface DefaultLogger
 *
 * @package Biller\BusinessLogic\Logger
 */
class DefaultLogger implements DefaultLoggerAdapter
{
    const MAX_BUFFER_SIZE = 100;

    const LOG_LEVEL_MAP = [
        Logger::ERROR => 'error',
        Logger::WARNING => 'warning',
        Logger::INFO => 'info',
        Logger::DEBUG => 'debug',
    ];

    /**
     * @var ExportLogMessage[]
     */
    protected static $buffer = [];

    /**
     * @inheritDoc
     */
    public function logMessage(LogData $data)
    {
        static::$buffer[] = $this->getMessageData($data);
        if (count(static::$buffer) >= static::MAX_BUFFER_SIZE) {
            $this->flush();
        }
    }

    public function flush()
    {
        if (empty(static::$buffer)) {
            return;
        }

        $exportRequest = ExportLogsRequest::create(static::$buffer);

        try {
            $this->getProxy()->exportLogs($exportRequest);
        } catch (Exception $e) {
            // Intentionally swallow exceptions during the export of logs sine there is nothing that can be done
        }


        static::$buffer = [];
    }

    /**
     * @param LogData $data
     * @return ExportLogMessage
     */
    private function getMessageData(LogData $data)
    {
        $message = ExportLogMessage::fromArray([
            'severity' => $this->getLogLevelLabel($data),
            'component' => $this->getShopConfiguration()->getIntegrationName(),
            'source' => $data->getComponent(),
            'message' => $data->getMessage() . $this->getLogContextAsString($data),
        ]);

        $context = $this->getLogContextAsArray($data);
        if (!empty($context['contractId'])) {
            $message->setContractId($context['contractId']);
        }

        return $message;
    }

    /**
     * @param LogData $data
     * @return string
     */
    private function getLogLevelLabel(LogData $data)
    {
        $label = (string)$data->getLogLevel();
        if (array_key_exists($data->getLogLevel(), static::LOG_LEVEL_MAP)) {
            $label = static::LOG_LEVEL_MAP[$data->getLogLevel()];
        }

        return $label;
    }

    /**
     * @param LogData $data
     * @return array
     */
    private function getLogContextAsArray(LogData $data)
    {
        $context = [];
        foreach ($data->getContext() as $logContextData) {
            $context[$logContextData->getName()] = $logContextData->getValue();
        }

        return $context;
    }

    /**
     * @param LogData $data
     * @return string
     */
    private function getLogContextAsString(LogData $data)
    {
        $contextData = array();
        foreach ($data->getContext() as $item) {
            $contextData[$item->getName()] = print_r($item->getValue(), true);
        }

        return !empty($contextData) ? PHP_EOL . 'Context data: ' . print_r($contextData, true) : '';
    }

    /**
     * Gets instance of configuration service.
     *
     * @return Configuration Instance of configuration service.
     *
     */
    private function getShopConfiguration()
    {
        return ServiceRegister::getService(Configuration::CLASS_NAME);
    }

    /**
     * Gets instance of logger proxy.
     *
     * @return LoggerProxy Instance of logger proxy.
     *
     */
    private function getProxy()
    {
        return ServiceRegister::getService(LoggerProxy::class);
    }
}