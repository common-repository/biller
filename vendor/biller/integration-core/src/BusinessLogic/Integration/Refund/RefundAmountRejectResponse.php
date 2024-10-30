<?php

namespace Biller\BusinessLogic\Integration\Refund;

class RefundAmountRejectResponse
{
    /**
     * @var bool
     */
    protected $isPermitted;

    /**
     * RejectResponse constructor.
     * @param bool $isPermitted
     */
    public function __construct($isPermitted)
    {
        $this->isPermitted = $isPermitted;
    }

    /**
     * @return bool
     */
    public function isPermitted()
    {
        return $this->isPermitted;
    }
}