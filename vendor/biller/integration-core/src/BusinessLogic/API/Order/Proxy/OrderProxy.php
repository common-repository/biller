<?php

namespace Biller\BusinessLogic\API\Order\Proxy;

use Biller\BusinessLogic\API\Authorization\Proxy\AuthorizedProxy;
use Biller\BusinessLogic\API\DTO\Refund\RefundRequest;
use Biller\BusinessLogic\API\DTO\Response\SuccessResponse;
use Biller\BusinessLogic\API\DTO\Shipment\ShipmentRequest;
use Biller\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use Biller\BusinessLogic\API\Http\Request\HttpRequest;
use Biller\BusinessLogic\API\Order\Response\RefundableLine;
use Biller\Domain\Amount\Amount;
use Biller\Domain\Amount\Currency;
use Biller\Domain\Amount\Tax;
use Biller\Domain\Amount\TaxableAmount;
use Biller\Domain\Exceptions\InvalidCurrencyCode;
use Biller\Domain\Exceptions\InvalidTaxPercentage;
use Biller\Domain\Order\Status;
use Biller\Domain\Refunds\RefundLine;
use Biller\Infrastructure\Http\Exceptions\HttpCommunicationException;
use Biller\Infrastructure\Http\Exceptions\HttpRequestException;
use Biller\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Generator;

/**
 * Class OrderProxy
 *
 * @package Biller\BusinessLogic\Order\Http
 */
class OrderProxy extends AuthorizedProxy
{
    /**
     * Capture the Order
     *
     * @param string $orderUUID
     * @param ShipmentRequest $shipmentRequest
     * @return SuccessResponse
     * @throws HttpCommunicationException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException|HttpRequestException
     */
    public function capture($orderUUID, ShipmentRequest $shipmentRequest)
    {
        $request = new HttpRequest("orders/{$orderUUID}/capture/", $shipmentRequest->toArray());

        return SuccessResponse::fromArray($this->post($request)->decodeBodyAsJson());
    }

    /**
     * Cancel order
     *
     * @param string $orderUUID
     * @return SuccessResponse
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     */
    public function cancel($orderUUID)
    {
        $request = new HttpRequest("orders/{$orderUUID}/cancel/");

        return SuccessResponse::fromArray($this->post($request)->decodeBodyAsJson());
    }

    /**
     * Refund the order
     *
     * @param string $orderUUID
     * @param RefundRequest $refundRequest
     * @return SuccessResponse
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     */
    public function refund($orderUUID, RefundRequest $refundRequest)
    {

        $request = new HttpRequest("orders/{$orderUUID}/refund/", $refundRequest->toArray());

        return SuccessResponse::fromArray($this->post($request)->decodeBodyAsJson());
    }

    /**
     * Create order status
     *
     * @throws HttpRequestException
     * @throws RequestNotSuccessfulException
     * @throws QueryFilterInvalidParamException
     * @throws HttpCommunicationException
     */
    public function getStatus($orderUUID)
    {
        $request = new HttpRequest("orders/$orderUUID/get_status/");

        try {
            $rawResponse = json_decode($this->get($request)->getBody(), true);
        } catch (HttpRequestException $e) {
            return Status::fromString("unknown-{$e->getCode()}");
        }

        return Status::fromString($rawResponse['status']);
    }

    /**
     * Fetch refunds from Biller for specified order
     *
     * @param string $orderUUID
     *
     * @return RefundLine[]
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws InvalidCurrencyCode
     * @throws InvalidTaxPercentage
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     */
    public function getRefunds($orderUUID)
    {
        return array_map(function(RefundableLine $refundableLine) {
            $currency = Currency::fromIsoCode($refundableLine->getCurrency());
            return new RefundLine(
                $refundableLine->getProductId(),
                TaxableAmount::fromAmounts(
                    Amount::fromInteger($this->getRefundedPriceExclTax($refundableLine), $currency),
                    Amount::fromInteger($this->getRefundedPriceInclTax($refundableLine), $currency)
                ),
                $refundableLine->getRefundableQuantity(),
                $refundableLine->getInvoiceUUID()
            );
        }, iterator_to_array($this->getRefundableLines($orderUUID)));
    }

    /**
     * Fetch total refundable amount from Biller by tax rate. Key in the response is the tax rate and value is sum of
     * refundable amounts for that tax rate and order invoice UUID.
     *
     * @param string $orderUUID
     * @return array<string, array<string, TaxableAmount>> Refundable amount totals grouped by the invoice uid and tax rate percentage.
     *
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws InvalidCurrencyCode
     * @throws InvalidTaxPercentage
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     * @throws \Biller\Domain\Exceptions\CurrencyMismatchException
     */
    public function getRefundableAmountTotals($orderUUID)
    {
        /**
         * @var array<string, array<string, TaxableAmount>> $totals
         */
        $totals = [];

        /**
         * @var RefundableLine $refundableLine
         */
        foreach ($this->getRefundableLines($orderUUID) as $refundableLine) {
            $invoiceUUID = $refundableLine->getInvoiceUUID();
            $currency = Currency::fromIsoCode($refundableLine->getCurrency());
            $lineAmount = TaxableAmount::fromAmounts(
                Amount::fromInteger($refundableLine->getTotalRefundableAmountExclTax(), $currency),
                Amount::fromInteger($refundableLine->getTotalRefundableAmountInclTax(), $currency)
            );
            $taxRate = (string)(new Tax($refundableLine->getProductRateTaxPercentage()));

            if (!array_key_exists($invoiceUUID, $totals) || !array_key_exists($taxRate, $totals[$invoiceUUID])) {
                $totals[$invoiceUUID][$taxRate] = $lineAmount;

                continue;
            }

            $totals[$invoiceUUID][$taxRate] = $totals[$invoiceUUID][$taxRate]->plus($lineAmount);
        }

        return $totals;
    }

    /**
     * @param RefundableLine $line
     * @return float|int
     */
    private function getRefundedPriceExclTax(RefundableLine $line)
    {
        return $line->getQuantity() * $line->getProductPriceExclTax() - $line->getTotalRefundableAmountExclTax();
    }

    /**
     * @param RefundableLine $line
     * @return float|int
     */
    private function getRefundedPriceInclTax(RefundableLine $line)
    {
        return $line->getQuantity() * $line->getProductPriceInclTax() - $line->getTotalRefundableAmountInclTax();
    }

    /**
     * @param string $orderUUID
     *
     * @return Generator
     * @throws HttpCommunicationException
     * @throws HttpRequestException
     * @throws QueryFilterInvalidParamException
     * @throws RequestNotSuccessfulException
     */
    private function getRefundableLines($orderUUID)
    {
        $endpoint = "orders/$orderUUID/refundable_invoices/";

        do {
            $request = new HttpRequest($endpoint);

            $response = json_decode($this->get($request)->getBody(), true);
            foreach ($response['results'] as $result) {
                foreach ($result['invoice_lines'] as $item) {
                    yield RefundableLine::fromArray(array_merge($item, ['uuid' => $result['uuid']]));
                }
            }

            $endpoint = $response['next'];
        } while ($endpoint !== null);
    }
}