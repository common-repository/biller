<?php

namespace Biller\BusinessLogic\Integration\Shipment;

use Biller\BusinessLogic\Integration\RejectResponse;
use Exception;

interface ShipmentService
{
    /**
     *  Reject shipment action
     *
     * @param $request
     * @param Exception $reason
     * @return RejectResponse
     */
    public function reject($request, Exception $reason);
}