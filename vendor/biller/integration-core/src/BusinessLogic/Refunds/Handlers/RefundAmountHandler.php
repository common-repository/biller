<?php

namespace Biller\BusinessLogic\Refunds\Handlers;

use Biller\BusinessLogic\API\DTO\Refund\Invoice;
use Biller\BusinessLogic\API\DTO\Refund\RefundAmount;
use Biller\BusinessLogic\API\DTO\Refund\RefundRequest;
use Biller\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use Biller\BusinessLogic\API\Order\Proxy\OrderProxy;
use Biller\BusinessLogic\Integration\Refund\RefundAmountRequestService;
use Biller\BusinessLogic\Integration\RefundAmountRequest;
use Biller\BusinessLogic\Notifications\NotificationHub;
use Biller\BusinessLogic\Notifications\NotificationText;
use Biller\BusinessLogic\Order\Exceptions\InvalidOrderReferenceException;
use Biller\BusinessLogic\Order\OrderReference\Repository\OrderReferenceRepository;
use Biller\BusinessLogic\Refunds\Contracts\RefundAmountHandlerService;
use Biller\Domain\Amount\Amount;
use Biller\Domain\Amount\Tax;
use Biller\Domain\Amount\TaxableAmount;
use Biller\Domain\Exceptions\CurrencyMismatchException;
use Biller\Domain\Exceptions\InvalidArgumentException;
use Biller\Domain\Exceptions\InvalidTaxPercentage;
use Biller\Infrastructure\Http\Exceptions\HttpCommunicationException;
use Biller\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Biller\Infrastructure\ServiceRegister;
use Exception;

class RefundAmountHandler implements RefundAmountHandlerService
{
    /**
     * @var OrderReferenceRepository
     */
    private $orderReferenceRepository;
    /**
     * @var OrderProxy
     */
    private $orderProxy;
    /**
     * @var RefundAmountRequestService
     */
    private $refundAmountRequestService;

    public function __construct(
        OrderReferenceRepository $orderReferenceRepository,
        OrderProxy $orderProxy,
        RefundAmountRequestService $refundAmountRequestService
    ) {
        $this->orderReferenceRepository = $orderReferenceRepository;
        $this->orderProxy = $orderProxy;
        $this->refundAmountRequestService = $refundAmountRequestService;
    }

    /**
     * @throws RequestNotSuccessfulException
     * @throws QueryFilterInvalidParamException
     * @throws HttpCommunicationException
     */
    public function handle(RefundAmountRequest $request)
    {
        if ($request->getRefundAmountTotal()->getAmount() <= 0) {
            throw new InvalidArgumentException('Refund amount must be greater than zero.');
        }

        $order = $this->orderReferenceRepository->findByExternalUUID($request->getShopOrderId());
        if (!$order) {
            throw new InvalidOrderReferenceException(
                "Order with {$request->getShopOrderId()} id doesn't have matching Biller order!"
            );
        }

        try {
            $refundRequest = new RefundRequest(
                $request->getRefundAmountTotal()->getAmount(),
                $request->getDescription(),
                $request->getExternalRefundUid()
            );
            $this->initRefundRequestDTO(
                $refundRequest,
                $request->getRefundAmountTotal(),
                $this->orderProxy->getRefundableAmountTotals($order->getBillerUUID())
            );

            $this->orderProxy->refund($order->getBillerUUID(), $refundRequest);
        } catch (Exception $e) {
            $response = $this->refundAmountRequestService->reject($request, $e);

            if (!$response->isPermitted()) {
	            NotificationHub::pushError(
		            new NotificationText('biller.payment.amount.refund.error.title'),
		            new NotificationText('biller.payment.amount.refund.error.description', array($e->getMessage())),
		            $request->getShopOrderId()
	            );

                $this->orderReferenceRepository->deleteBuExternalUUID($order->getExternalUUID());
            }
        }
    }

    /**
     * Initializes the refund request instance with invoices and refundable amounts
     *
     * @param RefundRequest $refundRequest
     * @param Amount $amountToRefund
     * @param array<string, array<string, TaxableAmount>> $refundableInvoiceTotals Refundable amount totals grouped by the invoice uid and tax rate percentage.
     * @return void
     * @throws CurrencyMismatchException
     * @throws InvalidTaxPercentage
     * @throws InvalidArgumentException
     */
    protected function initRefundRequestDTO(RefundRequest $refundRequest, Amount $amountToRefund, $refundableInvoiceTotals)
    {
        foreach ($refundableInvoiceTotals as $invoiceId => $refundableTotals) {
            if ($amountToRefund->getAmount() <= 0) {
                break;
            }

            $invoice = new Invoice($invoiceId, $refundRequest->getExternalRefundUid());
            foreach ($refundableTotals as $taxRate => $refundableTotal) {
                if ($refundableTotal->getAmountInclTax()->getAmount() <= 0) {
                    continue;
                }

                if ($amountToRefund->getAmount() <= 0) {
                    break;
                }

                $refundableAmount = $this->getRefundableAmount(
                    $refundableTotal,
                    TaxableAmount::fromAmountInclTaxAndTax($amountToRefund, new Tax((float)$taxRate))
                );

                $invoice->addRefundAmount(new RefundAmount(
                    $taxRate,
                    $refundableAmount->getAmountExclTax(),
                    $refundableAmount->getAmountInclTax(),
                    $refundRequest->getDescription()
                ));

                $amountToRefund = $amountToRefund->minus($refundableAmount->getAmountInclTax());
            }

            if ($invoice->hasRefundAmounts()) {
                $refundRequest->addInvoice($invoice);
            }
        }

        if ($amountToRefund->getAmount() > 0) {
            throw new InvalidArgumentException('Refund amount exceeds the refundable amount');
        }
    }

    /**
     * Gets the maximal refundable amount based on available total amount and amount that needs to be refunded
     *
     * @param TaxableAmount $availableForRefund
     * @param TaxableAmount $amountToRefund
     *
     * @return TaxableAmount
     */
    protected function getRefundableAmount(TaxableAmount $availableForRefund, TaxableAmount $amountToRefund)
    {
        if ($availableForRefund->getAmountInclTax()->getAmount() > $amountToRefund->getAmountInclTax()->getAmount()) {
            return $amountToRefund;
        }

        return $availableForRefund;
    }
}