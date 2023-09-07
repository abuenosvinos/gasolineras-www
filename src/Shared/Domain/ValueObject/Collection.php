<?php

declare(strict_types = 1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Util\Assert;
use ArrayIterator;
use Countable;
use IteratorAggregate;

abstract class Collection implements Countable, IteratorAggregate
{
    /** @var array */
    private $items;

    public function __construct(array $items)
    {
        Assert::arrayOf($this->type(), $items);

        $this->items = $items;
    }

    abstract protected function type(): string;

    public function getIterator()
    {
        return new ArrayIterator($this->items());
    }

    public function count()
    {
        return count($this->items());
    }
/*
    protected function each(callable $fn)
    {
        each($fn, $this->items());
    }
*/
    protected function items()
    {
        return $this->items;
    }
}
