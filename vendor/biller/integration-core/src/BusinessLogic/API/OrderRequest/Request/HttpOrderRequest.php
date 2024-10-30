<?php

namespace Biller\BusinessLogic\API\OrderRequest\Request;

use Biller\BusinessLogic\API\Http\Request\HttpRequest;
use Biller\Domain\Exceptions\CurrencyMismatchException;
use Biller\Domain\Order\OrderRequest;
use Biller\Domain\Order\OrderRequest\Address;
use Biller\Domain\Order\OrderRequest\Buyer;
use Biller\Domain\Order\OrderRequest\Company;
use Biller\Domain\Order\OrderRequest\Discount;
use Biller\Domain\Order\OrderRequest\OrderLine;
use Biller\Domain\Order\OrderRequest\OrderLines;

/**
 * Class HttpOrderRequest
 *
 * @package Biller\BusinessLogic\Order\OrderRequest\Http
 */
class HttpOrderRequest extends HttpRequest
{
    /**
     * Create HttpOrderRequest based on OrderRequest
     *
     * @param OrderRequest $request
     * @return HttpOrderRequest
     * @throws CurrencyMismatchException
     * @throws CurrencyMismatchException
     */
    public static function create(OrderRequest $request)
    {
        return new self('order-request/', self::getOrderRequestDTO($request));
    }

    /**
     * @param OrderRequest $request
     * @return array
     * @throws CurrencyMismatchException
     */
    private static function getOrderRequestDTO(OrderRequest $request)
    {
        $data = [
            'external_webshop_uid' => $request->getExternalWebshopUID(),
            'external_order_uid' => $request->getExternalOrderNumber(),
            'amount' => $request->getAmount()->getAmount(),
            'currency' => $request->getAmount()->getCurrency()->getIsoCode(),
            'order_lines' => self::getOrderLines($request->getOrderLines()),
            'buyer_company' => self::getBuyerCompany($request->getBuyerCompany()),
            'buyer_representative' => self::getBuyer($request->getBuyerRepresentative()),
            'shipping_address' => self::getAddress($request->getShippingAddress()),
            'billing_address' => self::getAddress($request->getBillingAddress()),
        ];

        if ($request->getLocale()) {
            $data['locale'] = $request->getLocale()->getLocale();
        }

        if ($request->getDiscount()) {
            $data['discount'] = self::getDiscount($request->getDiscount());
        }

        if ($request->getPaymentLinkDuration()) {
            $data['payment_link_duration'] = $request->getPaymentLinkDuration()->getDuration();
        }

        if ($request->getSuccessUrl()) {
            $data['seller_urls']['success_url'] = $request->getSuccessUrl();
        }

        if ($request->getSuccessUrl()) {
            $data['seller_urls']['error_url'] = $request->getErrorUrl();
        }

        if ($request->getSuccessUrl()) {
            $data['seller_urls']['cancel_url'] = $request->getCancelUrl();
        }

        if ($request->getWebhookUrl()) {
            $data['webhook_urls']['webhook_url'] = $request->getWebhookUrl();
        }

        return $data;
    }

    /**
     * @param OrderLines $orderLines
     * @return array
     * @throws \Biller\Domain\Exceptions\InvalidTaxPercentage
     */
    private static function getOrderLines(OrderLines $orderLines)
    {
        $orderLinesDTO = [];

        /**
         * @var OrderLine $orderLine;
         */
        foreach ($orderLines as $orderLine) {
            $orderLineDTO = [
                'quantity' => $orderLine->getQuantity(),
                'product_id' => $orderLine->getProductId(),
                'product_name' => $orderLine->getProductName(),
                'product_price_excl_tax' => $orderLine->getTaxableAmount()->getAmountExclTax()->getAmount(),
                'product_price_incl_tax' => $orderLine->getTaxableAmount()->getAmountInclTax()->getAmount(),
                'product_tax_rate_percentage' => $orderLine->getTaxRate()
            ];

            if ($orderLine->getProductDescription()) {
                $orderLineDTO['product_description'] = $orderLine->getProductDescription();
            }

            $orderLinesDTO[] = $orderLineDTO;
        }

        return $orderLinesDTO;
    }

    /**
     * @param Company $company
     * @return array
     */
    private static function getBuyerCompany(Company $company)
    {
        $buyerCompany = [
            'name' => $company->getName()
        ];

        if ($company->getRegistrationNumber()) {
            $buyerCompany['registration_number'] = $company->getRegistrationNumber();
        }
        if ($company->getVatNumber()) {
            $buyerCompany['vat_number'] = $company->getVatNumber();
        }
        if ($company->getWebsite()) {
            $buyerCompany['website'] = $company->getWebsite();
        }

        return $buyerCompany;
    }

    /**
     * @param Buyer $buyerRepresentative
     * @return array
     */
    private static function getBuyer(Buyer $buyerRepresentative)
    {
        $buyer = [
            'first_name' => $buyerRepresentative->getFirstName(),
            'last_name' => $buyerRepresentative->getLastName(),
            'email' => $buyerRepresentative->getEmail()
        ];

        if ($buyerRepresentative->getPhoneNumber()) {
            $buyer['phone_number'] = $buyerRepresentative->getPhoneNumber();
        }

        return $buyer;
    }

    /**
     * @param Address $address
     * @return array
     */
    private static function getAddress(Address $address)
    {
        $addressDTO = [
            'city' => $address->getCity(),
            'postal_code' => $address->getPostalCode(),
            'country' => $address->getCountry()->getCountry()
        ];

        if ($address->getPrimaryAddress()) {
            $addressDTO['street_address_1'] = $address->getPrimaryAddress();
        }

        if ($address->getSecondaryAddress()) {
            $addressDTO['street_address_2'] = $address->getSecondaryAddress();
        }

        if ($address->getRegion()) {
            $addressDTO['region'] = $address->getRegion();
        }

        return $addressDTO;
    }

    /**
     * @param Discount $discount
     * @return array
     * @throws CurrencyMismatchException
     */
    private static function getDiscount(Discount $discount)
    {
        $discountDTO = [
            'description' => $discount->getDescription(),
            'amount_incl_tax' => $discount->getAmount()->getAmountInclTax()->getAmount()
        ];

        if ($discount->getAmount()->getAmountExclTax()) {
            $discountDTO['amount_excl_tax'] = $discount->getAmount()->getAmountExclTax()->getAmount();
        }

        if ($discount->getAmount()->getTaxAmount() >= 0) {
            $discountDTO['amount_tax'] = $discount->getAmount()->getTaxAmount();
        }

        return $discountDTO;
    }
}