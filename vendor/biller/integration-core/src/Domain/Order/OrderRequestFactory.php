<?php

namespace Biller\Domain\Order;

use Biller\Domain\Amount\Amount;
use Biller\Domain\Amount\TaxableAmount;
use Biller\Domain\Exceptions\CurrencyMismatchException;
use Biller\Domain\Exceptions\InvalidArgumentException;
use Biller\Domain\Exceptions\InvalidTaxPercentage;
use Biller\Domain\Order\OrderRequest\Address;
use Biller\Domain\Order\OrderRequest\Buyer;
use Biller\Domain\Order\OrderRequest\Company;
use Biller\Domain\Order\OrderRequest\Discount;
use Biller\Domain\Order\OrderRequest\Locale;
use Biller\Domain\Order\OrderRequest\OrderLine;
use Biller\Domain\Order\OrderRequest\OrderLines;
use Biller\Domain\Order\OrderRequest\PaymentLinkDuration;

/**
 * Class OrderRequestFactory
 *
 * @package Biller\Domain\Order
 */
class OrderRequestFactory
{
    const ROUNDING = 'Rounding';
    
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
     * @var Discount[]
     */
    private $discounts;
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

    public function __construct()
    {
        $this->orderLines = new OrderLines();
    }

    /**
     * @return OrderRequest
     * 
     * @throws CurrencyMismatchException
     * @throws InvalidArgumentException
     * @throws InvalidTaxPercentage
     */
    public function create()
    {
        if (empty($this->externalWebshopUID)) {
            throw new InvalidArgumentException('External webshop UID missing!');
        }
        if (empty($this->externalOrderUID)) {
            throw new InvalidArgumentException('External order UID missing!');
        }
        if (empty($this->amount)) {
            throw new InvalidArgumentException('Amount is missing!');
        }
        if (empty($this->buyerCompany)) {
            throw new InvalidArgumentException('Buyer company missing!');
        }
        if (empty($this->buyerRepresentative)) {
            throw new InvalidArgumentException('Buyer information\'s missing!');
        }
        if (empty($this->shippingAddress)) {
            throw new InvalidArgumentException('Shipping address missing!');
        }
        if (empty($this->billingAddress)) {
            throw new InvalidArgumentException('Billing address missing!');
        }

        $amountDiff = $this->getAmountDiff();

        if ($amountDiff->getAmount() > 0) {
            $this->discounts[] = new Discount(
                self::ROUNDING,
                TaxableAmount::fromAmountInclTax($amountDiff)
            );
        }

        if ($amountDiff->getAmount() < 0) {
            $this->orderLines->offsetSet(
                self::ROUNDING,
                new OrderLine(
                    self::ROUNDING,
                    self::ROUNDING,
                    TaxableAmount::fromAmountInclTax(Amount::fromInteger(abs($amountDiff->getAmount()), $amountDiff->getCurrency())),
                    '0',
                    1,
                    self::ROUNDING
                )
            );
        }

        $orderRequest = new OrderRequest(
            $this->externalWebshopUID,
            $this->externalOrderUID,
            $this->amount,
            $this->orderLines,
            $this->buyerCompany,
            $this->buyerRepresentative,
            $this->shippingAddress,
            $this->billingAddress,
            $this->locale,
            $this->getDiscount(),
            $this->paymentLinkDuration,
            $this->successUrl,
            $this->errorUrl,
            $this->cancelUrl,
            $this->webhookUrl
        );

        if ($this->externalOrderNumber) {
            $orderRequest->setExternalOrderNumber($this->externalOrderNumber);
        }

        return $orderRequest;
    }

    /**
     * @param string $externalWebshopUID
     */
    public function setExternalWebshopUID($externalWebshopUID)
    {
        $this->externalWebshopUID = $externalWebshopUID;
    }

    /**
     * @param string $externalOrderUID
     */
    public function setExternalOrderUID($externalOrderUID)
    {
        $this->externalOrderUID = $externalOrderUID;
    }

    /**
     * @param string|null $externalOrderNumber
     */
    public function setExternalOrderNumber($externalOrderNumber)
    {
        $this->externalOrderNumber = $externalOrderNumber;
    }

    /**
     * @param Amount $amount
     */
    public function setAmount(Amount $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @param OrderLine $orderLine
     */
    public function addOrderLine(OrderLine $orderLine)
    {
        $this->orderLines->offsetSet($orderLine->getProductId(), $orderLine);
    }

    /**
     * @param Company $buyerCompany
     */
    public function setBuyerCompany(Company $buyerCompany)
    {
        $this->buyerCompany = $buyerCompany;
    }

    /**
     * @param Buyer $buyerRepresentative
     */
    public function setBuyerRepresentative(Buyer $buyerRepresentative)
    {
        $this->buyerRepresentative = $buyerRepresentative;
    }

    /**
     * @param Address $shippingAddress
     */
    public function setShippingAddress(Address $shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
    }

    /**
     * @param Address $billingAddress
     */
    public function setBillingAddress(Address $billingAddress)
    {
        $this->billingAddress = $billingAddress;
    }

    /**
     * @param Locale|null $locale
     */
    public function setLocale(Locale $locale = null)
    {
        $this->locale = $locale;
    }

    /**
     * @param Discount $discount
     */
    public function addDiscount(Discount $discount)
    {
        $this->discounts[] = $discount;
    }

    /**
     * @param PaymentLinkDuration|null $paymentLinkDuration
     */
    public function setPaymentLinkDuration(PaymentLinkDuration $paymentLinkDuration = null)
    {
        $this->paymentLinkDuration = $paymentLinkDuration;
    }

    /**
     * @param string|null $successUrl
     */
    public function setSuccessUrl($successUrl = null)
    {
        $this->successUrl = $successUrl;
    }

    /**
     * @param string|null $errorUrl
     */
    public function setErrorUrl($errorUrl = null)
    {
        $this->errorUrl = $errorUrl;
    }

    /**
     * @param string|null $cancelUrl
     */
    public function setCancelUrl($cancelUrl = null)
    {
        $this->cancelUrl = $cancelUrl;
    }

    /**
     * @param string|null $webhookUrl
     */
    public function setWebhookUrl($webhookUrl = null)
    {
        $this->webhookUrl = $webhookUrl;
    }

    /**
     * @return Amount
     * @throws CurrencyMismatchException
     * @throws InvalidArgumentException
     * @throws InvalidTaxPercentage
     */
    private function getAmountDiff()
    {
        $diff = $this->getOrderLinesSum()->minus($this->amount);
        if ($this->getDiscount()) {
            $diff = $diff->minus($this->getDiscount()->getAmount()->getAmountInclTax());
        }

        return $diff;
    }

    /**
     * @return Amount
     * @throws CurrencyMismatchException
     */
    private function getOrderLinesSum()
    {
	    $orderLinesSum = Amount::fromInteger(0, $this->amount->getCurrency());
	    /**
	     * @var OrderLine $orderLine
	     */
	    foreach ($this->orderLines as $orderLine) {
		    $orderLinesSum = $orderLinesSum->plus(Amount::fromInteger(
			    $orderLine->getTaxableAmount()->getAmountInclTax()->getAmount() * $orderLine->getQuantity(),
			    $orderLine->getTaxableAmount()->getAmountInclTax()->getCurrency()));
	    }

	    return $orderLinesSum;
    }

    /**
     * @throws InvalidArgumentException
     * @throws InvalidTaxPercentage
     * @throws CurrencyMismatchException
     */
    private function getDiscount()
    {
        if(empty($this->discounts)) {
            return null;
        }
        $description = '';
        $priceInclTax = Amount::fromInteger(0, $this->amount->getCurrency());
        $priceExclTax = Amount::fromInteger(0, $this->amount->getCurrency());
        foreach ($this->discounts as $key => $discount) {

            if ($key + 1 !== count($this->discounts)) {
                $description .= "{$discount->getDescription()}; ";
            } else {
                $description .= $discount->getDescription();
            }

            $priceInclTax = $priceInclTax->plus($discount->getAmount()->getAmountInclTax());
            $priceExclTax = $priceExclTax->plus($discount->getAmount()->getAmountExclTax());
        }

        return new Discount($description, TaxableAmount::fromAmounts($priceExclTax, $priceInclTax));
    }
}