<?php

namespace Biller\Infrastructure\ORM;

use Biller\Infrastructure\ORM\Exceptions\RepositoryClassException;
use Biller\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use Biller\Infrastructure\ORM\Interfaces\RepositoryInterface;

/**
 * Class RepositoryRegistry.
 *
 * @package Biller\Infrastructure\ORM
 */
class RepositoryRegistry
{
    /**
     * @var RepositoryInterface[]
     */
    protected static $instantiated = array();
    /**
     * @var array
     */
    protected static $repositories = array();

    /**
     * Returns an instance of repository that is responsible for handling the entity
     *
     * @param string $entityClass Class name of entity.
     *
     * @return RepositoryInterface
     *
     * @throws RepositoryNotRegisteredException
     */
    public static function getRepository($entityClass)
    {
        if (!static::isRegistered($entityClass)) {
            throw new RepositoryNotRegisteredException("Repository for entity $entityClass not found or registered.");
        }

        if (!array_key_exists($entityClass, static::$instantiated)) {
            $repositoryClass = static::$repositories[$entityClass];
            /** @var RepositoryInterface $repository */
            $repository = new $repositoryClass();
            $repository->setEntityClass($entityClass);
            static::$instantiated[$entityClass] = $repository;
        }

        return static::$instantiated[$entityClass];
    }

    /**
     * Registers repository for provided entity class
     *
     * @param string $entityClass Class name of entity.
     * @param string $repositoryClass Class name of repository.
     *
     * @throws RepositoryClassException
     */
    public static function registerRepository($entityClass, $repositoryClass)
    {
        if (!is_subclass_of($repositoryClass, RepositoryInterface::CLASS_NAME)) {
            throw new RepositoryClassException("Class $repositoryClass is not implementation of RepositoryInterface.");
        }

        unset(self::$instantiated[$entityClass]);
        self::$repositories[$entityClass] = $repositoryClass;
    }

    /**
     * Checks whether repository has been registered for a particular entity.
     *
     * @param string $entityClass Entity for which check has to be performed.
     *
     * @return boolean Returns TRUE if repository has been registered; FALSE otherwise.
     */
    public static function isRegistered($entityClass)
    {
        return isset(static::$repositories[$entityClass]);
    }
}
