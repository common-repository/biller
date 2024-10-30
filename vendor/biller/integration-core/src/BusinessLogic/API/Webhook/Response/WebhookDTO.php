<?php

namespace Biller\BusinessLogic\API\Webhook\Response;

use Biller\Infrastructure\Data\DataTransferObject;

/**
 * Class WebhookDTO
 *
 * @package Biller\BusinessLogic\API\DTO\Webhook
 */
class WebhookDTO extends DataTransferObject
{
    /**
     * @var string
     */
    private $created;
    /**
     * @var string
     */
    private $eventType;
    /**
     * @var array
     */
    private $payload;


    public static function fromArray(array $data)
    {
        $webhook = new self();
        $webhook->setCreated(static::getDataValue($data, 'created'));
        $webhook->setEventType(static::getDataValue($data, 'event_type'));
        $webhook->setPayload(static::getDataValue($data, 'payload', []));

        return $webhook;
    }

    /**
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return string
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @param string $eventType
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;
    }

    /**
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param array $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }


    public function toArray()
    {
        return [
            'created' => $this->getCreated(),
            'event_type' => $this->getEventType(),
            'payload' => $this->getPayload()
        ];
    }
}