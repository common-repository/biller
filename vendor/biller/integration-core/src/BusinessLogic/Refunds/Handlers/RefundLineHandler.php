<?php

namespace Biller\BusinessLogic\Refunds\Handlers;

use Biller\BusinessLogic\API\DTO\Refund\Invoice;
use Biller\BusinessLogic\API\DTO\Refund\RefundLine;
use Biller\BusinessLogic\API\DTO\Refund\RefundRequest;
use Biller\BusinessLogic\API\DTO\Response\RejectResponse;
use Biller\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use Biller\BusinessLogic\API\Order\Proxy\OrderProxy;
use Biller\BusinessLogic\Integration\Order\OrderStatusTransitionService;
use Biller\BusinessLogic\Integration\RefundLineRequest;
use Biller\BusinessLogic\Notifications\NotificationHub;
use Biller\BusinessLogic\Notifications\NotificationText;
use Biller\BusinessLogic\Order\OrderReference\Repository\OrderReferenceRepository;
use Biller\Domain\Exceptions\CurrencyMismatchException;
use Biller\Domain\Exceptions\InvalidCurrencyCode;
use Biller\Domain\Exceptions\InvalidTaxPercentage;
use Biller\Domain\Exceptions\InvalidTypeException;
use Biller\Domain\Order\OrderRequest\Discount;
use Biller\Domain\Refunds\RefundCollection;
use Biller\Infrastructure\Http\Exceptions\HttpCommunicationException;
use Biller\Infrastructure\Http\Exceptions\HttpRequestException;
use Biller\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Biller\Infrastructure\ServiceRegister;

class RefundLineHandler
{
    /**
     * @var OrderStatusTransitionService
     */
    protected $orderStatusTransitionService;

    /**
     * @var OrderProxy
     */
    protected $orderProxy;

    /**
     * @var OrderReferenceRepository
     */
    protected $orderReferenceRepository;

    /**
     * @param RefundLineRequest $request
     *
     * @return void
     * @throws HttpCommunicationException
     * @throws InvalidCurrencyCode
     * @throws InvalidTaxPercentage
     * @throws InvalidTypeException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     * @throws CurrencyMismatchException
     */
    public function handle(RefundLineRequest $request)
    {
        if ($order = $this->getOrderReferenceRepository()->findByExternalUUID($request->getShopOrderId())) {
            try {
                $refundLines = $this->getOrderProxy()->getRefunds($order->getBillerUUID());
                $refundCollection = new RefundCollection($refundLines);
                $missingRefundItems = $refundCollection->findMissing(new RefundCollection($request->getRefundLines()))->getItems();

                $refundRequest = $this->createRefundRequest($request, $missingRefundItems);
                $this->getOrderProxy()->refund($order->getBillerUUID(), $refundRequest);

            } catch (HttpRequestException $exception) {
                $response = RejectResponse::fromArray(json_decode($exception->getMessage(), true));
                $isRejected = $this->getOrderStatusTransitionService()->rejectRefund($order->getBillerUUID(), $response);

				if(!$isRejected) {
					NotificationHub::pushError(
						new NotificationText('biller.payment.refund.line.error.title'),
						new NotificationText('biller.payment.refund.line.error.description', array($exception->getMessage())),
						$request->getShopOrderId()
					);

					$this->orderReferenceRepository->deleteBuExternalUUID($order->getExternalUUID());
				}
            }
        }
    }

    /**
     * Create refund request
     *
     * @param RefundLineRequest $refundLineRequest
     * @param array $missingRefundItems
     * @return RefundRequest
     */
    protected function createRefundRequest(RefundLineRequest $refundLineRequest, array $missingRefundItems)
    {
        $totalAmount = $this->getTotalAmount($missingRefundItems, $refundLineRequest->getDiscount());
        $request = new RefundRequest($totalAmount, $refundLineRequest->getDescription(), $refundLineRequest->getExternalRefundUid());
        $request->setInvoices([new Invoice($refundLineRequest->getInvoiceUUID(), $refundLineRequest->getExternalRefundUid(), $missingRefundItems)]);

        return $request;
    }

    /**
     * @param RefundLine[] $missingRefundItems
     * @param Discount $discount
     * @return int
     */
    private function getTotalAmount(array $missingRefundItems, Discount $discount)
    {
        $amount = 0;
        foreach ($missingRefundItems as $refundItem) {
            $amount += $refundItem->getRefundedAmountIncTax() - $discount->getAmount()->getAmountInclTax();
        }

        return $amount;
    }

    /**
     * @return OrderStatusTransitionService
     */
    protected function getOrderStatusTransitionService()
    {
        if (!$this->orderStatusTransitionService) {
            $this->orderStatusTransitionService = ServiceRegister::getService(OrderStatusTransitionService::class);
        }

        return $this->orderStatusTransitionService;
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