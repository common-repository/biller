<?php

namespace Biller\BusinessLogic\Order;

use Biller\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use Biller\BusinessLogic\API\Order\Proxy\OrderProxy;
use Biller\BusinessLogic\API\OrderRequest\Proxy\OrderRequestProxy;
use Biller\BusinessLogic\API\OrderRequest\Request\HttpOrderRequest;
use Biller\BusinessLogic\Integration\Order\OrderStatusTransitionService;
use Biller\BusinessLogic\Integration\Refund\OrderRefundService;
use Biller\BusinessLogic\Order\Exceptions\InvalidOrderReferenceException;
use Biller\BusinessLogic\Order\OrderReference\Entities\OrderReference;
use Biller\BusinessLogic\Order\OrderReference\Repository\OrderReferenceRepository;
use Biller\Domain\Exceptions\CurrencyMismatchException;
use Biller\Domain\Exceptions\InvalidCurrencyCode;
use Biller\Domain\Exceptions\InvalidTaxPercentage;
use Biller\Domain\Exceptions\InvalidTypeException;
use Biller\Domain\Order\OrderRequest;
use Biller\Domain\Refunds\RefundCollection;
use Biller\Infrastructure\Http\Exceptions\HttpCommunicationException;
use Biller\Infrastructure\Http\Exceptions\HttpRequestException;
use Biller\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Biller\Infrastructure\ServiceRegister;

/**
 * Class OrderService
 *
 * @package Biller\BusinessLogic\Order
 */
class OrderService
{
    /**
     * @var OrderReferenceRepository
     */
    private $orderReferenceRepository;

    /**
     * @var OrderStatusTransitionService
     */
    private $orderStatusTransitionService;

    /**
     * @var OrderRefundService
     */
    private $orderRefundService;

    /**
     * @param OrderReferenceRepository $orderReferenceRepository
     * @param OrderStatusTransitionService $orderStatusTransitionService
     * @param OrderRefundService $orderRefundService
     */
    public function __construct(
        OrderReferenceRepository $orderReferenceRepository,
        OrderStatusTransitionService $orderStatusTransitionService,
        OrderRefundService $orderRefundService
    ) {
        $this->orderStatusTransitionService = $orderStatusTransitionService;
        $this->orderReferenceRepository = $orderReferenceRepository;
        $this->orderRefundService = $orderRefundService;
    }

    /**
     * Create order request on Biller and returns payment page url
     *
     * @throws HttpRequestException
     * @throws RequestNotSuccessfulException
     * @throws QueryFilterInvalidParamException
     * @throws HttpCommunicationException|CurrencyMismatchException
     * @throws CurrencyMismatchException
     */
    public function create(OrderRequest $orderRequest)
    {
        $orderRequestResponse = $this->getOrderRequestProxy()->createOrderRequest(HttpOrderRequest::create($orderRequest));

        $this->orderReferenceRepository->save($orderRequest->getExternalOrderUID(), $orderRequestResponse->getUuid());

        return $orderRequestResponse->getPaymentPageUrl();
    }

    /**
     * Get of payment status on Biller is accepted
     *
     * @param string $externalOrderUUID
     * @return bool
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws InvalidOrderReferenceException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     */
    public function isPaymentAccepted($externalOrderUUID)
    {
        return $this->getStatus($externalOrderUUID)->isAccepted();
    }

    /**
     * Get payment status on Biller
     *
     * @throws HttpRequestException
     * @throws InvalidOrderReferenceException
     * @throws RequestNotSuccessfulException
     * @throws QueryFilterInvalidParamException
     * @throws HttpCommunicationException
     */
    public function getStatus($externalOrderUUID)
    {
        $orderReference = $this->orderReferenceRepository->findByExternalUUID($externalOrderUUID);
        if ($orderReference === null) {
            throw new InvalidOrderReferenceException("Order with $externalOrderUUID id doesn't exits!");
        }

        return $this->getOrderProxy()
            ->getStatus($orderReference->getBillerUUID());
    }

    /**
     * Update order status
     *
     * @param OrderReference $orderReference
     * @return void
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     * @throws InvalidTypeException
     * @throws InvalidCurrencyCode
     * @throws InvalidTaxPercentage
     */
    public function updateStatus(OrderReference $orderReference)
    {
        $orderStatus = $this->getOrderProxy()->getStatus($orderReference->getBillerUUID());

        if ($orderStatus->isRefunded()) {
            $this->orderRefundService->refund($orderReference->getExternalUUID());
        }

        if ($orderStatus->isRefundedPartially()) {
            $this->partiallyRefundOrder($orderReference);
        }

        $this->orderStatusTransitionService->updateStatus($orderReference->getExternalUUID(), $orderStatus);
    }

    /**
     * @return OrderRequestProxy
     */
    protected function getOrderRequestProxy()
    {
        return ServiceRegister::getService(OrderRequestProxy::class);
    }

    /**
     * @return OrderProxy
     */
    protected function getOrderProxy()
    {
        return ServiceRegister::getService(OrderProxy::class);
    }

    /**
     * @param OrderReference $orderReference
     * @return void
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws InvalidTypeException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     * @throws InvalidCurrencyCode|InvalidTaxPercentage
     */
    private function partiallyRefundOrder(OrderReference $orderReference)
    {
        $refundableLines = $this->getOrderProxy()->getRefunds($orderReference->getBillerUUID());
        $billerRefunds = new RefundCollection($refundableLines);

        $this->orderRefundService->refund($orderReference->getExternalUUID(), $billerRefunds);
    }
}