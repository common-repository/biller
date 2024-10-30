<?php

namespace Biller\BusinessLogic\Notifications\Interfaces;

use Biller\BusinessLogic\Notifications\Model\Notification;

/**
 * Interface NotificationChannel
 *
 * @package Biller\BusinessLogic\Notifications\Interfaces
 */
interface NotificationChannelAdapter
{

    /**
     *
     * @param Notification $notification
     */
    public function push(Notification $notification);
}
