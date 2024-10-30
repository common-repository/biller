<?php

namespace Biller\Domain\Order\OrderRequest;

use ArrayAccess;
use ArrayIterator;
use Biller\Domain\Exceptions\InvalidTypeException;
use Biller\Domain\Exceptions\OutOfRangeException;
use Countable;
use IteratorAggregate;

/**
 * Class OrderLines
 *
 * @package Biller\Domain\Order
 */
class OrderLines implements Countable, IteratorAggregate, ArrayAccess
{
    /**
     * @var OrderLine[]
     */
    protected $items = [];

    /**
     * Creates a new collection.
     *
     * @param OrderLine[] $items array with all the objects to be added. They must be of the OrderLine class
     * @throws InvalidTypeException
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            if (!($item instanceof OrderLine)) {
                throw new InvalidTypeException();
            }

            $this->offsetSet($item->getProductId(), $item);
        }
    }

    /**
     * Returns the count of items in the collection.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Returns an iterator
     * Implements IteratorAggregate
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @param string $offset
     * @param OrderLine $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->items[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    /**
     * get an offset's value
     * Implements ArrayAccess
     *
     * @param integer $offset
     * @return OrderLine
     * @throws OutOfRangeException
     * @see get
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new OutOfRangeException();
        }

        return $this->items[$offset];
    }

    /**
     * Determine if offset exists
     * Implements ArrayAccess
     *
     * @param integer $offset
     * @return boolean
     * @see exists
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->items);
    }

}