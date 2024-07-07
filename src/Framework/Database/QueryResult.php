<?php

namespace Framework\Database;

use ArrayAccess;
use Exception;
use Iterator;

class QueryResult implements ArrayAccess, Iterator
{

    private array $records;
    private ?string $entity;
    private int $index = 0;
    private array $hydratedRecords = [];

    public function __construct(array $records, ?string $entity = null)
    {
        $this->records = $records;
        $this->entity = $entity;
    }

    /** Retrieves an element at the defined index */
    public function get(int $index): mixed
    {
        if ($this->entity) {
            if (!isset($this->hydratedRecords[$index])) {
                $this->hydratedRecords[$index] = Hydrator::hydrate($this->records[$index], $this->entity);
            }

            return $this->hydratedRecords[$index];
        }

        return $this->entity;
    }

    /** Return the current element */
    public function current(): mixed
    {
        return $this->get($this->index);
    }

    /**Move forward to next element */
    public function next(): void
    {
        $this->index++;
    }

    /** Return the key of the current element */
    public function key(): int
    {
        return $this->index;
    }

    /** Checks if current position is valid */
    public function valid(): bool
    {
        return isset($this->records[$this->index]);
    }

    /** Rewind the Iterator to the first element */
    public function rewind(): void
    {
        $this->index = 0;
    }

    /** Checks whether an offset exists */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->records[$offset]);
    }

    /** Offset to retrieve */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     *
     * @throws Exception
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new Exception("Can't alter records");
    }

    /**
     * Offset to unset
     *
     * @throws Exception
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new Exception("Can't alter records");
    }
}
