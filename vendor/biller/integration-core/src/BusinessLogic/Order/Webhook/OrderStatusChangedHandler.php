<?php

namespace Biller\BusinessLogic\Order\Webhook;

use Biller\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use Biller\BusinessLogic\API\Webhook\Response\WebhookOrderDTO;
use Biller\BusinessLogic\Integration\Authorization\UserInfoRepository;
use Biller\BusinessLogic\Notifications\NotificationHub;
use Biller\BusinessLogic\Notifications\NotificationText;
use Biller\BusinessLogic\Order\OrderReference\Repository\OrderReferenceRepository;
use Biller\BusinessLogic\Order\OrderService;
use Biller\BusinessLogic\Webhook\WebhookHandlerInterface;
use Biller\Domain\Exceptions\InvalidCurrencyCode;
use Biller\Domain\Exceptions\InvalidTaxPercentage;
use Biller\Domain\Exceptions\InvalidTypeException;
use Biller\Infrastructure\Http\Exceptions\HttpCommunicationException;
use Biller\Infrastructure\Http\Exceptions\HttpRequestException;
use Biller\Infrastructure\Logger\Logger;
use Biller\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Biller\Infrastructure\ServiceRegister;
use Exception;

/**
 * Class OrderStatusChangedHandler
 *
 * @package Biller\BusinessLogic\Order\Webhook
 */
class OrderStatusChangedHandler implements WebhookHandlerInterface
{
    /**
     * @var WebhookOrderDTO
     */
    private $payload;

    /**
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->payload = WebhookOrderDTO::fromArray($payload);
    }

    /**
     * Handel order status changed event
     *
     * @throws HttpRequestException
     * @throws InvalidCurrencyCode
     * @throws RequestNotSuccessfulException
     * @throws QueryFilterInvalidParamException
     * @throws InvalidTypeException
     * @throws HttpCommunicationException|InvalidTaxPercentage
     */
    public function handle()
    {
        if (!$this->isValid($this->payload)) {
            return;
        }

	    $orderReference = $this->getOrderReferenceRepository()->findByBillerUUID($this->payload->getOrderId());

	    if ($orderReference === null) {
		    return;
	    }

        try {
            $this->getOrderService()->updateStatus($orderReference);

        } catch (Exception $e) {
            Logger::logWarning('Order status update failed! Error message: ' . $e->getMessage());

	        NotificationHub::pushInfo(
		        new NotificationText('biller.payment.webhook.notification.order_status_changed_error.title'),
		        new NotificationText(
			        'biller.payment.webhook.notification.order_status_changed_error.description',
			        array('message' => $e->getMessage())
		        ),
		        $orderReference->getExternalUUID()
	        );

            throw $e;
        }
    }

    /**
     * @param WebhookOrderDTO $orderPayload
     * @return bool
     */
    private function isValid(WebhookOrderDTO $orderPayload)
    {
        $userInfo = $this->getUserInfoRepository()->getActiveUserInfo();

        return $userInfo && $userInfo->getWebShopUID() === $orderPayload->getWebShopId();
    }

    /**
     * @return OrderService
     */
    private function getOrderService()
    {
        return ServiceRegister::getService(OrderService::class);
    }

    /**
     * @return OrderReferenceRepository
     */
    private function getOrderReferenceRepository()
    {
        return ServiceRegister::getService(OrderReferenceRepository::class);
    }

    /**
     * @return UserInfoRepository
     */
    private function getUserInfoRepository()
    {
        return ServiceRegister::getService(UserInfoRepository::class);
    }
}