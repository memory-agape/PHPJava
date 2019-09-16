<?php
namespace PHPJava\Compiler\Builder\Collection;

use PHPJava\Compiler\Builder\EntryInterface;
use PHPJava\Compiler\Builder\Structures\EntryCollectionInterface;
use PHPJava\Exceptions\NotAllowedDeleteException;

abstract class AbstractEntryCollection implements EntryCollectionInterface, \ArrayAccess, \IteratorAggregate
{
    protected $entries = [];

    public function add(EntryInterface $entry): EntryCollectionInterface
    {
        $entryNumber = count($this->entries);
        $this->entries[] = $entry;
        return $this;
    }

    public function offsetGet($offset)
    {
        return $this->entries[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (!($value instanceof EntryInterface)) {
            throw new NotAllowedDeleteException('The value is not allowed type.');
        }
        $this->entries[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        throw new NotAllowedDeleteException('The entry cannot delete because the entry is an immutable.');
    }

    public function offsetExists($offset)
    {
        return isset($this->entries[$offset]);
    }

    public function toArray(): array
    {
        return $this->entries;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->entries);
    }
}