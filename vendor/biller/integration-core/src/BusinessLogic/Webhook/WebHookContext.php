<?php

namespace Biller\BusinessLogic\Webhook;

use Biller\Infrastructure\Logger\Logger;

/**
 * Class WebHookContext
 *
 * @package Biller\BusinessLogic\Webhook
 */
class WebHookContext
{
    /**
     * @var int
     */
    private static $startLevel = 0;

    /**
     * Starts execution context
     */
    public static function start()
    {
        static::$startLevel++;
        Logger::logDebug('WebHook context execution started.', 'Core', ['level' => static::$startLevel]);
    }

    /**
     * Stops execution context
     */
    public static function stop()
    {
        static::$startLevel--;
        Logger::logDebug('WebHook context execution stopped.', 'Core', ['level' => static::$startLevel]);
    }

    /**
     * Wraps provided callback into a callback that executes only if web hook context is not started
     *
     * @param callable $callback
     *
     * @return callable
     */
    public static function getProtectedCallable(callable $callback)
    {
        return function () use ($callback) {
            if (!WebHookContext::isStarted()) {
                return call_user_func_array($callback, func_get_args());
            }

            return null;
        };
    }

    /**
     * @return bool
     */
    public static function isStarted()
    {
        return static::$startLevel > 0;
    }
}
