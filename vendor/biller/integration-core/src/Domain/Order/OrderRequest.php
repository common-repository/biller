<?php

namespace Biller\Domain\Order;

use Biller\Domain\Amount\Amount;
use Biller\Domain\Order\OrderRequest\Address;
use Biller\Domain\Order\OrderRequest\Buyer;
use Biller\Domain\Order\OrderRequest\Company;
use Biller\Domain\Order\OrderRequest\Discount;
use Biller\Domain\Order\OrderRequest\Locale;
use Biller\Domain\Order\OrderRequest\OrderLines;
use Biller\Domain\Order\OrderRequest\PaymentLinkDuration;

/**
 * Class OrderRequest
 *
 * @package Biller\Domain\Order
 */
class OrderRequest
{
    /**
     * @var string
     */
    private $externalWebshopUID;
    /**
     * @var string
     */
    private $externalOrderUID;
    /**
     * @var string|null
     */
    private $externalOrderNumber;
    /**
     * @var Amount
     */
    private $amount;
    /**
     * @var OrderLines
     */
    private $orderLines;
    /**
     * @var Company
     */
    private $buyerCompany;
    /**
     * @var Buyer
     */
    private $buyerRepresentative;
    /**
     * @var Address
     */
    private $shippingAddress;
    /**
     * @var Address
     */
    private $billingAddress;
    /**
     * @var Locale|null
     */
    private $locale;
    /**
     * @var Discount|null
     */
    private $discount;
    /**
     * @var PaymentLinkDuration|null
     */
    private $paymentLinkDuration;
    /**
     * @var string|null
     */
    private $successUrl;
    /**
     * @var string|null
     */
    private $errorUrl;
    /**
     * @var string|null
     */
    private $cancelUrl;
    /**
     * @var string|null
     */
    private $webhookUrl;

    /**
     * @param string $externalWebshopUID
     * @param string $externalOrderUID
     * @param Amount $amount
     * @param OrderLines $orderLines
     * @param Company $buyerCompany
     * @param Buyer $buyerRepresentative
     * @param Address $shippingAddress
     * @param Address $billingAddress
     * @param Locale|null $locale
     * @param Discount|null $discount
     * @param PaymentLinkDuration|null $paymentLinkDuration
     * @param string|null $successUrl
     * @param string|null $errorUrl
     * @param string|null $cancelUrl
     * @param string|null $webhookUrl
     */
    public function __construct(
        $externalWebshopUID,
        $externalOrderUID,
        Amount $amount,
        OrderLines $orderLines,
        Company $buyerCompany,
        Buyer $buyerRepresentative,
        Address $shippingAddress,
        Address $billingAddress,
        Locale $locale = null,
        Discount $discount = null,
        PaymentLinkDuration $paymentLinkDuration = null,
        $successUrl = null,
        $errorUrl = null,
        $cancelUrl = null,
        $webhookUrl = null
    ) {

        $this->externalWebshopUID = (string)$externalWebshopUID;
        $this->externalOrderUID = (string)$externalOrderUID;
        $this->amount = $amount;
        $this->orderLines = $orderLines;
        $this->buyerCompany = $buyerCompany;
        $this->buyerRepresentative = $buyerRepresentative;
        $this->shippingAddress = $shippingAddress;
        $this->billingAddress = $billingAddress;
        $this->setLocale($locale);
        $this->discount = $discount;
        $this->setPaymentLinkDuration($paymentLinkDuration);
        $this->successUrl = $successUrl;
        $this->errorUrl = $errorUrl;
        $this->cancelUrl = $cancelUrl;
        $this->webhookUrl = $webhookUrl;
    }

    /**
     * @return string
     */
    public function getExternalWebshopUID()
    {
        return $this->externalWebshopUID;
    }

    /**
     * @param string $externalWebshopUID
     */
    public function setExternalWebshopUID($externalWebshopUID)
    {
        $this->externalWebshopUID = $externalWebshopUID;
    }

    /**
     * Gets the unique identifier of the order in the shop system, usually the order id is used.
     *
     * @return string
     */
    public function getExternalOrderUID()
    {
        return $this->externalOrderUID;
    }

    /**
     * Sets the unique identifier of the order in the shop system, usually the order id is used.
     *
     * @param string $externalOrderUID
     */
    public function setExternalOrderUID($externalOrderUID)
    {
        $this->externalOrderUID = $externalOrderUID;
    }


    /**
     * Gets the order number in the shop system, usually the visual representation of the oder identifier in the system.
     * By default, the @see getExternalOrderUID will be used unless value is explicity set by the integration.
     *
     * @return string
     */
    public function getExternalOrderNumber()
    {
        return $this->externalOrderNumber ?: $this->getExternalOrderUID();
    }

    /**
     * sets the order number in the shop system. If order number and order id are different uses this method to set
     * the visual representation of the oder identifier. By default, the @see getExternalOrderUID will be used.
     *
     * @param string $externalOrderNumber
     */
    public function setExternalOrderNumber($externalOrderNumber)
    {
        $this->externalOrderNumber = $externalOrderNumber;
    }

    /**
     * @return Amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param Amount $amount
     */
    public function setAmount(Amount $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return OrderLines
     */
    public function getOrderLines()
    {
        return $this->orderLines;
    }

    /**
     * @param OrderLines $orderLines
     */
    public function setOrderLines(OrderLines $orderLines)
    {
        $this->orderLines = $orderLines;
    }

    /**
     * @return Company
     */
    public function getBuyerCompany()
    {
        return $this->buyerCompany;
    }

    /**
     * @param Company $buyerCompany
     */
    public function setBuyerCompany(Company $buyerCompany)
    {
        $this->buyerCompany = $buyerCompany;
    }

    /**
     * @return Buyer
     */
    public function getBuyerRepresentative()
    {
        return $this->buyerRepresentative;
    }

    /**
     * @param Buyer $buyerRepresentative
     */
    public function setBuyerRepresentative(Buyer $buyerRepresentative)
    {
        $this->buyerRepresentative = $buyerRepresentative;
    }

    /**
     * @return Address
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * @param Address $shippingAddress
     */
    public function setShippingAddress(Address $shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
    }

    /**
     * @return Address
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * @param Address $billingAddress
     */
    public function setBillingAddress(Address $billingAddress)
    {
        $this->billingAddress = $billingAddress;
    }

    /**
     * @return Locale|null
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param Locale|null $locale
     */
    public function setLocale(Locale $locale = null)
    {
        $this->locale = $locale;
    }

    /**
     * @return Discount|null
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param Discount|null $discount
     */
    public function setDiscount(Discount $discount = null)
    {
        $this->discount = $discount;
    }

    /**
     * @return PaymentLinkDuration|null
     */
    public function getPaymentLinkDuration()
    {
        return $this->paymentLinkDuration;
    }

    /**
     * @param PaymentLinkDuration|null $paymentLinkDuration
     */
    public function setPaymentLinkDuration(PaymentLinkDuration $paymentLinkDuration = null)
    {
        $this->paymentLinkDuration = $paymentLinkDuration;
    }

    /**
     * @return string|null
     */
    public function getSuccessUrl()
    {
        return $this->successUrl;
    }

    /**
     * @param string|null $successUrl
     */
    public function setSuccessUrl($successUrl = null)
    {
        $this->successUrl = $successUrl;
    }

    /**
     * @return string|null
     */
    public function getErrorUrl()
    {
        return $this->errorUrl;
    }

    /**
     * @param string|null $errorUrl
     */
    public function setErrorUrl($errorUrl = null)
    {
        $this->errorUrl = $errorUrl;
    }

    /**
     * @return string|null
     */
    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }

    /**
     * @param string|null $cancelUrl
     */
    public function setCancelUrl($cancelUrl = null)
    {
        $this->cancelUrl = $cancelUrl;
    }

    /**
     * @return string|null
     */
    public function getWebhookUrl()
    {
        return $this->webhookUrl;
    }
    /**
     * @param string|null $webhookUrl
     */
    public function setWebhookUrl($webhookUrl = null)
    {
        $this->webhookUrl = $webhookUrl;
    }

}