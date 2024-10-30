<?php

namespace Biller\BusinessLogic\Webhook;

use Biller\BusinessLogic\API\Webhook\Response\WebhookDTO;
use Biller\BusinessLogic\Order\Webhook\OrderStatusChangedHandler;

/**
 * Class WebhookFactory
 *
 * @package Biller\BusinessLogic\Webhook
 */
class WebhookFactory
{
    const ORDER_STATUS_CHANGED = 'order_status_changed';
    const DEFAULT_STATUS = 'default';

    private static $handlersMap = [
        self::ORDER_STATUS_CHANGED => OrderStatusChangedHandler::class,
        self::DEFAULT_STATUS => NullStatusHandler::class
    ];

    public static function fromPayload($payload)
    {
        $webhookDTO = WebhookDTO::fromArray(json_decode($payload, true));

        if (array_key_exists($webhookDTO->getEventType(), self::$handlersMap)) {
            return new self::$handlersMap[$webhookDTO->getEventType()]($webhookDTO->getPayload());
        }

        return new self::$handlersMap[self::DEFAULT_STATUS]();
    }
}