<?php

namespace Biller\BusinessLogic\Cancellation;

use Biller\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use Biller\BusinessLogic\API\Order\Proxy\OrderProxy;
use Biller\BusinessLogic\Integration\Cancellation\CancellationService;
use Biller\BusinessLogic\Integration\CancellationRequest;
use Biller\BusinessLogic\Notifications\NotificationHub;
use Biller\BusinessLogic\Notifications\NotificationText;
use Biller\BusinessLogic\Order\OrderReference\Repository\OrderReferenceRepository;
use Biller\Infrastructure\Http\Exceptions\HttpCommunicationException;
use Biller\Infrastructure\Http\Exceptions\HttpRequestException;
use Biller\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Biller\Infrastructure\ServiceRegister;

class CancellationHandler
{
    /**
     * @var CancellationService
     */
    protected $cancellationService;

    /**
     * @var OrderProxy
     */
    protected $orderProxy;

    /**
     * @var OrderReferenceRepository
     */
    protected $orderReferenceRepository;

    /**
     * @param CancellationRequest $request
     * @return void
     * @throws RequestNotSuccessfulException
     * @throws HttpCommunicationException
     * @throws QueryFilterInvalidParamException
     */
    public function handle(CancellationRequest $request)
    {
        if ($order = $this->getOrderReferenceRepository()->findByExternalUUID($request->getShopOrderId())) {
            if ($request->isPartialCancellation()) {
                $orderLines = $this->getCancellationService()->getAllItems($request->getShopOrderId());
                foreach ($orderLines as $orderLine) {
                    if ($orderLine->isCancelled()) {
                        return;
                    }
                }
            }
            try {
                $this->getOrderProxy()->cancel($order->getBillerUUID());
            } catch (HttpRequestException $exception) {
                $response = $this->getCancellationService()->reject($request, $exception);

                if (!$response->isPermitted()) {
                    NotificationHub::pushError(
                        new NotificationText('biller.payment.order.cancellation.title'),
                        new NotificationText('biller.payment.order.cancellation.description', array($exception->getMessage())),
                        $request->getShopOrderId()
                    );

                    $this->getOrderReferenceRepository()->deleteBuExternalUUID($order->getExternalUUID());
                }
            }
        }
    }

    /**
     * @return CancellationService
     */
    protected function getCancellationService()
    {
        if (!$this->cancellationService) {
            $this->cancellationService = ServiceRegister::getService(CancellationService::class);
        }

        return $this->cancellationService;
    }

    /**
     * @return OrderProxy
     */
    protected function getOrderProxy()
    {
        if (!$this->orderProxy) {
            $this->orderProxy = ServiceRegister::getService(OrderProxy::class);
        }

        return $this->orderProxy;
    }

    /**
     * @return OrderReferenceRepository
     */
    protected function getOrderReferenceRepository()
    {
        if (!$this->orderReferenceRepository) {
            $this->orderReferenceRepository = ServiceRegister::getService(OrderReferenceRepository::class);
        }

        return $this->orderReferenceRepository;
    }
}