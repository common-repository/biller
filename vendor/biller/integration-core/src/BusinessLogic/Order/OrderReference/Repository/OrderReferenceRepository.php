<?php

namespace Biller\BusinessLogic\Order\OrderReference\Repository;

use Biller\BusinessLogic\Order\OrderReference\Entities\OrderReference;
use Biller\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Biller\Infrastructure\ORM\Interfaces\RepositoryInterface;
use Biller\Infrastructure\ORM\QueryFilter\QueryFilter;
use Biller\Infrastructure\ORM\RepositoryRegistry;

/**
 * Class OrderReferenceRepository
 *
 * @package Biller\BusinessLogic\OrderReference\Repository
 */
class OrderReferenceRepository
{
    /**
     * Save order reference
     *
     * @param string $externalOrderUUID
     * @param string $billerOrderUUID
     * @return OrderReference
     * @throws QueryFilterInvalidParamException
     */
    public function save($externalOrderUUID, $billerOrderUUID)
    {
        $this->deleteBuExternalUUID($externalOrderUUID);

        $orderReference = $this->createOrderReference($externalOrderUUID, $billerOrderUUID);

        $this->getRepository()->save($orderReference);

        return $orderReference;
    }

    /**
     * Find order reference by external order UUID
     *
     * @param string $externalOrderUUID
     * @return OrderReference|null
     * @throws QueryFilterInvalidParamException
     */
    public function findByExternalUUID($externalOrderUUID)
    {
        $filter = new QueryFilter();
        /** @noinspection PhpUnhandledExceptionInspection */
        $filter->where('externalUUID', '=', (string)$externalOrderUUID);

        /** @var OrderReference $orderReference */
        $orderReference = $this->getRepository()->selectOne($filter);

        return $orderReference;
    }

    /**
     * Find order reference by biller order UUID
     *
     * @param $billerUUID
     * @return OrderReference|null
     * @throws QueryFilterInvalidParamException
     */
    public function findByBillerUUID($billerUUID)
    {
        $filter = new QueryFilter();
        /** @noinspection PhpUnhandledExceptionInspection */
        $filter->where('billerUUID', '=', $billerUUID);

        /** @var OrderReference $orderReference */
        $orderReference = $this->getRepository()->selectOne($filter);

        return $orderReference;
    }

    /**
     * Deletes order reference by external order id
     *
     * @param string $externalOrderUUID
     * @return void
     * @throws QueryFilterInvalidParamException
     */
    public function deleteBuExternalUUID($externalOrderUUID)
    {
        $deleteFilter = new QueryFilter();
        $deleteFilter->where('externalUUID', '=', $externalOrderUUID);
        $this->getRepository()->deleteWhere($deleteFilter);
    }

    /**
     * Deletes order reference by Biller order id
     *
     * @param string $orderId
     * @return void
     * @throws QueryFilterInvalidParamException
     */
    public function deleteByOrderId($orderId)
    {
        $deleteFilter = new QueryFilter();
        $deleteFilter->where('billerUUID', '=', $orderId);
        $this->getRepository()->deleteWhere($deleteFilter);
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * Returns repository instance.
     *
     * @return RepositoryInterface Configuration repository.
     */
    protected function getRepository()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return RepositoryRegistry::getRepository(OrderReference::getClassName());
    }

    /**
     * Create order reference object
     *
     * @param string $externalOrderUUID
     * @param string $billerOrderUUID
     * @return OrderReference
     */
    private function createOrderReference($externalOrderUUID, $billerOrderUUID)
    {
        $orderReference = new OrderReference();
        $orderReference->setBillerUUID($billerOrderUUID);
        $orderReference->setExternalUUID($externalOrderUUID);

        return $orderReference;
    }
}