<?php

namespace Biller\BusinessLogic\API\OrderRequest\Response;

use Biller\Infrastructure\Data\DataTransferObject;

/**
 * Interface OrderRequestResponse
 *
 * @package Biller\BusinessLogic\OrderRequest\DTO
 */
class OrderRequestResponse extends DataTransferObject
{
    /**
     * @var string
     */
    private $uuid;
    /**
     * @var string
     */
    private $paymentPageUrl;

    /**
     * @param string $uuid
     * @param string $paymentPageUrl
     */
    public function __construct($uuid, $paymentPageUrl)
    {
        $this->uuid = $uuid;
        $this->paymentPageUrl = $paymentPageUrl;
    }

    /**
     * Creates instance of UserInfo.
     *
     * @param array $data
     *
     * @return OrderRequestResponse
     */
    public static function fromArray(array $data)
    {
        return new self($data['uuid'], $data['payment_page_url']);
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return array(
            'uuid' => $this->getUuid(),
            'payment_page_url' => $this->getPaymentPageUrl(),
        );
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getPaymentPageUrl()
    {
        return $this->paymentPageUrl;
    }

    /**
     * @param string $paymentPageUrl
     */
    public function setPaymentPageUrl($paymentPageUrl)
    {
        $this->paymentPageUrl = $paymentPageUrl;
    }
}