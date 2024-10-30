<?php

namespace Biller\BusinessLogic\API\Logger\Request;

use Biller\Infrastructure\Data\DataTransferObject;

/**
 * Class ExportLogMessage
 *
 * @package Biller\BusinessLogic\API\Logger\Request
 */
class ExportLogMessage extends DataTransferObject
{
    /**
     * @var string
     */
    protected $severity = '';
    /**
     * @var string
     */
    protected $component = '';
    /**
     * @var string
     */
    protected $contractId = '';
    /**
     * @var string
     */
    protected $source = '';
    /**
     * @var string
     */
    protected $message = '';

    /**
     * @param array $data
     * @return ExportLogMessage
     */
    public static function fromArray(array $data)
    {
        $instance = new self();
        $instance->setSeverity(static::getDataValue($data, 'severity'));
        $instance->setComponent(static::getDataValue($data, 'component'));
        $instance->setSource(static::getDataValue($data, 'source'));
        $instance->setMessage(static::getDataValue($data, 'message'));
        $instance->setContractId(static::getDataValue($data, 'contractId'));

        return $instance;
    }

    /**
     * @param ExportLogMessage[] $batch
     * @return array
     */
    public static function toBatchArray(array $batch)
    {
        $result = [];

        foreach ($batch as $item) {
            $result[] = $item->toArray();
        }

        return $result;
    }
    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'severity' => $this->getSeverity(),
            'component' => $this->getComponent(),
            'source' => $this->getSource(),
            'message' => $this->getMessage(),
            'contractId' => $this->getContractId(),
        ];
    }

    /**
     * @return string
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * @param string $severity
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;
    }

    /**
     * @return string
     */
    public function getComponent()
    {
        return $this->component;
    }

    /**
     * @param string $component
     */
    public function setComponent($component)
    {
        $this->component = $component;
    }

    /**
     * @return string
     */
    public function getContractId()
    {
        return $this->contractId;
    }

    /**
     * @param string $contractId
     */
    public function setContractId($contractId)
    {
        $this->contractId = $contractId;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
}