<?php

namespace Biller\BusinessLogic\Webhook;

/**
 * Class NullStatusHandler
 *
 * @package Biller\BusinessLogic\Webhook
 */
class NullStatusHandler implements WebhookHandlerInterface
{
    /**
     * Handel unknown status
     */
    public function handle()
    {
    }
}