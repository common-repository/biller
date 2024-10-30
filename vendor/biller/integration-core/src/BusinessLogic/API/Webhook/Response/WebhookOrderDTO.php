<?php

namespace Biller\BusinessLogic\API\Webhook\Response;

use Biller\Infrastructure\Data\DataTransferObject;

/**
 * Class WebhookOrderDTO
 *
 * @package Biller\BusinessLogic\API\DTO\Webhook
 */
class WebhookOrderDTO extends DataTransferObject
{
    /**
     * @var string
     */
    private $orderId;
    /**
     * @var string
     */
    private $webShopId;

    public static function fromArray(array $data)
    {
        $webhookOrder = new self();
        $webhookOrder->setOrderId(static::getDataValue($data, 'order_id'));
        $webhookOrder->setWebShopId(static::getDataValue($data, 'webshop_id'));

        return $webhookOrder;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return string
     */
    public function getWebShopId()
    {
        return $this->webShopId;
    }

    /**
     * @param string $webShopId
     */
    public function setWebShopId($webShopId)
    {
        $this->webShopId = $webShopId;
    }

    public function toArray()
    {
        return [
            'order_id' => $this->orderId,
            'webshop_id' => $this->webShopId
        ];
    }
}