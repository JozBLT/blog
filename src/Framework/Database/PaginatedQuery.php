<?php

namespace Framework\Database;

use Pagerfanta\Adapter\AdapterInterface;
use PDO;

class PaginatedQuery implements AdapterInterface
{
    private Query $query;

    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /** Returns the number of results */
    public function getNbResults(): int
    {
        return $this->query->count();
    }

    /** Returns a slice of the results */
    public function getSlice(int $offset, int $length): QueryResult
    {
        $query = clone $this->query;

        return $query->limit($length, $offset)->fetchAll();
    }
}
