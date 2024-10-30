<?php

namespace Biller\BusinessLogic\Order\OrderReference\Entities;

use Biller\Infrastructure\ORM\Configuration\EntityConfiguration;
use Biller\Infrastructure\ORM\Configuration\IndexMap;
use Biller\Infrastructure\ORM\Entity;

/**
 * Class OrderReference
 *
 * @package Biller\BusinessLogic\OrderReference\Entities
 */
class OrderReference extends Entity
{
    const CLASS_NAME = __CLASS__;

    protected $fields = ['id', 'externalUUID', 'billerUUID'];

    /**
     * @var string
     */
    protected $externalUUID;
    /**
     * @var string
     */
    protected $billerUUID;

    /**
     * @return string
     */
    public function getExternalUUID()
    {
        return $this->externalUUID;
    }

    /**
     * @param string $externalUUID
     */
    public function setExternalUUID($externalUUID)
    {
        $this->externalUUID = $externalUUID;
    }

    /**
     * @return string
     */
    public function getBillerUUID()
    {
        return $this->billerUUID;
    }

    /**
     * @param string $billerUUID
     */
    public function setBillerUUID($billerUUID)
    {
        $this->billerUUID = $billerUUID;
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        $indexMap = new IndexMap();
        $indexMap->addStringIndex('externalUUID')
            ->addStringIndex('billerUUID');

        return new EntityConfiguration($indexMap, 'OrderReference');
    }
}