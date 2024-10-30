<?php

namespace Biller\BusinessLogic\Shipment;

use Biller\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use Biller\BusinessLogic\API\Order\Proxy\OrderProxy;
use Biller\BusinessLogic\Integration\Shipment\ShipmentService;
use Biller\BusinessLogic\Integration\ShipmentRequest;
use Biller\BusinessLogic\Notifications\NotificationHub;
use Biller\BusinessLogic\Notifications\NotificationText;
use Biller\BusinessLogic\Order\OrderReference\Repository\OrderReferenceRepository;
use Biller\Domain\Order\OrderRequest\OrderLine;
use Biller\Infrastructure\Http\Exceptions\HttpCommunicationException;
use Biller\Infrastructure\Http\Exceptions\HttpRequestException;
use Biller\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Biller\Infrastructure\ServiceRegister;
use Biller\BusinessLogic\API\DTO\Shipment\ShipmentRequest as ShipmentRequestDto;
use Biller\BusinessLogic\API\DTO\Shipment\OrderLine as ShipmentLine;

class ShipmentHandler
{
    /**
     * @param ShipmentRequest $shipmentRequest
     * @return void
     * @throws HttpCommunicationException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     */
    public function handle(ShipmentRequest $shipmentRequest)
    {
        if ($order = $this->getOrderReferenceRepository()->findByExternalUUID($shipmentRequest->getShopOrderId())) {
            try {
                $this->getOrderProxy()->capture($order->getBillerUUID(), $this->createShipmentDto($shipmentRequest));
            } catch (HttpRequestException $exception) {
                $response = $this->getShipmentService()->reject($shipmentRequest, $exception);

                if (!$response->isPermitted()) {
                    NotificationHub::pushError(
                        new NotificationText('biller.payment.order.capture.title'),
                        new NotificationText('biller.payment.order.capture.description', array($exception->getMessage())),
                        $shipmentRequest->getShopOrderId()
                    );

                    $this->getOrderReferenceRepository()->deleteBuExternalUUID($order->getExternalUUID());
                }
            }
        }
    }

    /**
     * Create shipment DTO from shipment request
     *
     * @param ShipmentRequest $request
     * @return ShipmentRequestDto
     */
    protected function createShipmentDto(ShipmentRequest $request)
    {
        return new ShipmentRequestDto(
            $request->getExternalInvoiceUid(),
            $request->getDiscount(),
            $request->getTotalAmount() ? $request->getTotalAmount()->getAmount() : '',
            array_map(static function (OrderLine $orderLine) {
                return new ShipmentLine(
                    $orderLine->getProductId(),
                    $orderLine->getQuantity(),
                    $orderLine->getTaxableAmount()->getAmountExclTax()->getAmount(),
                    $orderLine->getTaxableAmount()->getAmountInclTax()->getAmount(),
	                (string)$orderLine->getTaxableAmount()->getTax()
                );
            }, $request->getOrderLines())
        );
    }

    /**
     * @return ShipmentService
     */
    protected function getShipmentService()
    {
        return ServiceRegister::getService(ShipmentService::class);
    }

    /**
     * @return OrderProxy
     */
    protected function getOrderProxy()
    {
        return ServiceRegister::getService(OrderProxy::class);
    }

    /**
     * @return OrderReferenceRepository
     */
    protected function getOrderReferenceRepository()
    {
        return ServiceRegister::getService(OrderReferenceRepository::class);
    }
}