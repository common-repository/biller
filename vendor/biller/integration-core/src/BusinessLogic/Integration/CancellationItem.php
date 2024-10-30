<?php

namespace Biller\BusinessLogic\Integration;

class CancellationItem
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var bool
     */
    protected $cancelled;

    /**
     * @param string $id
     * @param bool $cancelled
     */
    public function __construct($id, $cancelled)
    {
        $this->id = $id;
        $this->cancelled = $cancelled;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function isCancelled()
    {
        return $this->cancelled;
    }

    /**
     * @param bool $cancelled
     */
    public function setCancelled($cancelled)
    {
        $this->cancelled = $cancelled;
    }
}