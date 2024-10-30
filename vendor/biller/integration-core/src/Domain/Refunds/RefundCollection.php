<?php

namespace Biller\Domain\Refunds;

use Biller\Domain\Amount\TaxableAmount;
use Biller\Domain\Exceptions\CurrencyMismatchException;
use Biller\Domain\Exceptions\InvalidTaxPercentage;
use Biller\Domain\Exceptions\InvalidTypeException;
use Countable;

/**
 * Class RefundCollection
 *
 * @package Biller\Domain\Refunds
 */
class RefundCollection implements Countable
{
    /**
     * @var RefundLine[]
     */
    protected $items = [];

    /**
     * Creates a new collection.
     *
     * @param RefundLine[] $items array with all the objects to be added. They must be of the RefundLine class
     *
     * @throws InvalidTypeException
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            if ( ! ($item instanceof RefundLine)) {
                throw new InvalidTypeException();
            }

            $this->addItem($item);
        }
    }

    /**
     * @return RefundLine[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Find difference between collections and return collection of new refund lines
     *
     * @param RefundCollection $oldRefunds
     *
     * @return RefundCollection
     *
     * @throws CurrencyMismatchException
     * @throws InvalidTaxPercentage
     * @throws InvalidTypeException
     */
    public function findNew(RefundCollection $oldRefunds)
    {
        return new self(array_reduce($this->items, function (array $carry, RefundLine $line) use ($oldRefunds) {
            $diff = $oldRefunds->getByProductId($line->getProductId());

            if ( ! $diff) {
                $carry[] = $line;
            } elseif ($newLine = $this->getNewRefundLine($line, $diff)) {
                $carry[] = $newLine;
            }

            return $carry;
        }, []));
    }

    /**
     * Find missing return items
     *
     * @param RefundCollection $shopRefunds
     *
     * @return RefundCollection
     *
     * @throws CurrencyMismatchException
     * @throws InvalidTaxPercentage
     * @throws InvalidTypeException
     */
    public function findMissing(RefundCollection $shopRefunds)
    {
        return new self(array_reduce($shopRefunds->getItems(), function (array $carry, RefundLine $line) {
            $diff = $this->getByProductId($line->getProductId());

            if ( ! $diff) {
                $carry[] = $line;
            } elseif ($newLine = $this->getNewRefundLine($line, $diff)) {
                $carry[] = $newLine;
            }

            return $carry;
        }, []));
    }

    /**
     * Get total refunded amount
     *
     * @return TaxableAmount
     * @throws InvalidTaxPercentage
     */
    public function getTotalRefunded()
    {
        return array_reduce($this->items, static function ($total, RefundLine $refundLine) {
            return $total === null ?
                $refundLine->getAmount() :
                $total->plus($refundLine->getAmount());
        }, null);
    }

    /**
     * Returns the count of items in the collection.
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Get an offset's value
     *
     * @param int|string $offset
     * @return RefundLine|null
     */
    public function getByProductId($offset)
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    /**
     * @param RefundLine $item
     */
    private function addItem(RefundLine $item)
    {
        $this->items[$item->getProductId()] = $item;
    }

    /* @param RefundLine $line
     * @param RefundLine $diff
     * @return RefundLine|null
     * @throws CurrencyMismatchException
     * @throws InvalidTaxPercentage
     */
    private function getNewRefundLine(RefundLine $line, RefundLine $diff)
    {
        $diffAmount = $line->getAmount()->minus($diff->getAmount());

        return $diffAmount->getAmountInclTax()->getAmount() > 0 ?
            new RefundLine($line->getProductId(), $diffAmount) : null;
    }
}