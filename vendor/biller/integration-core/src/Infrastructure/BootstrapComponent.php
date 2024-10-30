<?php

namespace Biller\Infrastructure;

use Biller\Infrastructure\Configuration\ConfigurationManager;
use Biller\Infrastructure\Http\CurlHttpClient;
use Biller\Infrastructure\Http\HttpClient;
use Biller\Infrastructure\Utility\Events\EventBus;
use Biller\Infrastructure\Utility\GuidProvider;
use Biller\Infrastructure\Utility\TimeProvider;

/**
 * Class BootstrapComponent.
 *
 * @package Biller\Infrastructure
 */
class BootstrapComponent
{
    /**
     * Initializes infrastructure components.
     */
    public static function init()
    {
        static::initServices();
        static::initRepositories();
        static::initEvents();
    }

    /**
     * Initializes services and utilities.
     */
    protected static function initServices()
    {
        ServiceRegister::registerService(
            ConfigurationManager::CLASS_NAME,
            function () {
                return ConfigurationManager::getInstance();
            }
        );
        ServiceRegister::registerService(
            TimeProvider::CLASS_NAME,
            function () {
                return TimeProvider::getInstance();
            }
        );
        ServiceRegister::registerService(
            GuidProvider::CLASS_NAME,
            function () {
                return GuidProvider::getInstance();
            }
        );
        ServiceRegister::registerService(
            EventBus::CLASS_NAME,
            function () {
                return EventBus::getInstance();
            }
        );

	    ServiceRegister::registerService(
		    HttpClient::CLASS_NAME,
		    function () {
			    return new CurlHttpClient();
		    }
	    );
    }

    /**
     * Initializes repositories.
     */
    protected static function initRepositories()
    {
    }

    /**
     * Initializes events.
     */
    protected static function initEvents()
    {
    }
}
