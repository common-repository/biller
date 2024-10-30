<?php

namespace Biller\BusinessLogic\Webhook;

use Biller\BusinessLogic\API\Webhook\Response\WebhookDTO;
use Biller\BusinessLogic\API\Webhook\Response\WebhookOrderDTO;
use Biller\BusinessLogic\Notifications\NotificationHub;
use Biller\BusinessLogic\Notifications\NotificationText;
use Biller\BusinessLogic\Order\OrderReference\Repository\OrderReferenceRepository;
use Biller\Infrastructure\Logger\Logger;

/**
 * Class WebhookHandler
 *
 * @package Biller\BusinessLogic\Webhook
 */
class WebhookHandler
{

    /**
     * @var OrderReferenceRepository
     */
    private $orderReferenceRepository;

    public function __construct(OrderReferenceRepository $orderReferenceRepository)
    {
        $this->orderReferenceRepository = $orderReferenceRepository;
    }

    /**
     * Routes for webhook events.
     *
     * @param string $payload
     * @return void
     */
    public function handle($payload)
    {
        try {
            WebHookContext::start();

            $handler = WebhookFactory::fromPayload($payload);
            $handler->handle();

            WebHookContext::stop();
        } catch (\Exception $exception) {
            Logger::logError('Webhook handling failed', 'Core', [
                'ExceptoionMessage' => $exception->getMessage(),
                'ExceptoionTrace' => $exception->getTraceAsString(),
            ]);

            $this->handleWebhookException(
                $exception,
                WebhookDTO::fromArray(json_decode($payload, true))
            );
        }
    }

    /**
     * @param \Exception $exception
     * @param WebhookDTO $webhookData
     *
     * @return void
     */
    protected function handleWebhookException(\Exception $exception, WebhookDTO $webhookData)
    {
        $webhookPayload = WebhookOrderDTO::fromArray($webhookData->getPayload());
        $orderId = $webhookPayload->getOrderId();

        $orderReference = $this->orderReferenceRepository->findByBillerUUID($orderId);
        if (!$orderReference) {
            return;
        }

        NotificationHub::pushError(
            new NotificationText('biller.payment.webhook.error.title'),
            new NotificationText(
                'biller.payment.webhook.error.description',
                [$orderReference->getExternalUUID(), $exception->getMessage()]
            ),
            $orderReference->getExternalUUID()
        );
        $this->orderReferenceRepository->deleteByOrderId($orderId);
    }
}