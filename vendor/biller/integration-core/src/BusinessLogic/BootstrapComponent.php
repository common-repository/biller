<?php

namespace Biller\BusinessLogic;


use Biller\BusinessLogic\API\Authorization\Proxy\TokenProxy;
use Biller\BusinessLogic\API\Logger\Proxy\LoggerProxy;
use Biller\BusinessLogic\API\Order\Proxy\OrderProxy;
use Biller\BusinessLogic\API\OrderRequest\Proxy\OrderRequestProxy;
use Biller\BusinessLogic\Authorization\AuthorizationService;
use Biller\BusinessLogic\Integration\Order\OrderStatusTransitionService;
use Biller\BusinessLogic\Integration\Refund\OrderRefundService;
use Biller\BusinessLogic\Integration\Refund\RefundAmountRequestService;
use Biller\BusinessLogic\Logger\DefaultLogger;
use Biller\BusinessLogic\Order\OrderReference\Repository\OrderReferenceRepository;
use Biller\BusinessLogic\Order\OrderService;
use Biller\BusinessLogic\Refunds\Contracts\RefundAmountHandlerService;
use Biller\BusinessLogic\Refunds\Handlers\RefundAmountHandler;
use Biller\BusinessLogic\Webhook\WebhookHandler;
use Biller\Infrastructure\Http\HttpClient;
use Biller\Infrastructure\Logger\Interfaces\DefaultLoggerAdapter;
use Biller\Infrastructure\ServiceRegister;

/**
 * Class BootstrapComponent
 *
 * @package Biller\BusinessLogic
 */
class BootstrapComponent extends \Biller\Infrastructure\BootstrapComponent
{
    /**
     * Initializes services and utilities.
     */
    protected static function initServices()
    {
        parent::initServices();

        ServiceRegister::registerService(
            AuthorizationService::class,
            static function () {
                return AuthorizationService::getInstance();
            }
        );
        ServiceRegister::registerService(
            OrderService::class,
            static function () {
                return new OrderService(
                    ServiceRegister::getService(OrderReferenceRepository::class),
                    ServiceRegister::getService(OrderStatusTransitionService::class),
                    ServiceRegister::getService(OrderRefundService::class)
                );
            }
        );
        ServiceRegister::registerService(
            TokenProxy::class,
            static function () {
                /** @var HttpClient $client */
                $client = ServiceRegister::getService(HttpClient::class);
                /** @var AuthorizationService $authService */
                $authService = ServiceRegister::getService(Authorization\Contracts\AuthorizationService::class);

                return new TokenProxy($client, $authService->getUserInfo()->getMode());
            }
        );

        ServiceRegister::registerService(
            OrderRequestProxy::class,
            static function () {
                /** @var HttpClient $client */
                $client = ServiceRegister::getService(HttpClient::class);
                /** @var AuthorizationService $authService */
                $authService = ServiceRegister::getService(Authorization\Contracts\AuthorizationService::class);

                return new OrderRequestProxy($client, $authService->getUserInfo()->getMode(),
                    (string)$authService->getValidAccessToken());
            }
        );
        ServiceRegister::registerService(
            OrderProxy::class,
            static function () {
                /** @var HttpClient $client */
                $client = ServiceRegister::getService(HttpClient::class);
                /** @var AuthorizationService $authService */
                $authService = ServiceRegister::getService(Authorization\Contracts\AuthorizationService::class);

                return new OrderProxy($client, $authService->getUserInfo()->getMode(),
                    (string)$authService->getValidAccessToken());
            }
        );
        ServiceRegister::registerService(
            OrderReferenceRepository::class,
            static function () {
                return new OrderReferenceRepository();
            }
        );

        ServiceRegister::registerService(
            DefaultLoggerAdapter::class,
            static function () {
                $defaultLogger = new DefaultLogger();

                // Make sure that any leftover log message is transferred to the Pricemonitor API
                register_shutdown_function([$defaultLogger, 'flush']);

                return $defaultLogger;
            }
        );

        ServiceRegister::registerService(
            LoggerProxy::class,
            static function () {
                /** @var HttpClient $client */
                $client = ServiceRegister::getService(HttpClient::class);
                /** @var AuthorizationService $authService */
                $authService = ServiceRegister::getService(Authorization\Contracts\AuthorizationService::class);

                return new LoggerProxy($client, $authService->getUserInfo()->getMode(),
                    (string)$authService->getValidAccessToken());
            }
        );

        ServiceRegister::registerService(
            RefundAmountHandlerService::class,
            static function () {
                return new RefundAmountHandler(
                    ServiceRegister::getService(OrderReferenceRepository::class),
                    ServiceRegister::getService(OrderProxy::class),
                    ServiceRegister::getService(RefundAmountRequestService::class)
                );
            }
        );

        ServiceRegister::registerService(
            WebhookHandler::class,
            static function () {
                return new WebhookHandler(
                    ServiceRegister::getService(OrderReferenceRepository::class)
                );
            }
        );
    }

    /**
     * Initialize events
     */
    protected static function initEvents()
    {
        parent::initEvents();
    }

    /**
     * Initializes repositories.
     *
     * @return void
     */
    protected static function initRepositories()
    {
        parent::initRepositories();

        ServiceRegister::registerService(
            OrderReferenceRepository::class,
            static function () {
                return new OrderReferenceRepository();
            }
        );
    }
}
