<?php

namespace Framework\Database;

use IteratorAggregate;
use Pagerfanta\Pagerfanta;
use PDO;
use PDOStatement;
use Traversable;

class Query implements IteratorAggregate
{

    private ?array $select = null;

    private array $from;

    private array $where = [];

    private ?string $entity;

    private array $order;

    private ?string $limit = null;

    private array $joins;

    private ?PDO $pdo;

    private array $params = [];

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    /** Defines the FROM */
    public function from(string $table, ?string $alias = null): self
    {
        if ($alias) {
            $this->from[$table] = $alias;
        } else {
            $this->from[] = $table;
        }

        return $this;
    }

    /** Specifies which fields to retrieve */
    public function select(string ...$fields): self
    {
        $this->select = $fields;

        return $this;
    }

    /** Specifies the limit */
    public function limit(int $length, int $offset = 0): self
    {
        $this->limit = "$offset, $length";

        return $this;
    }

    /** Specifies the recovery order */
    public function order(string $order): self
    {
        $this->order[] = $order;

        return $this;
    }

    /** Adds a LEFT JOIN */
    public function join(string $table, string $condition, string $type = "left"): self
    {
        $this->joins[$type][] = [$table, $condition];

        return $this;
    }

    /** Defines the recovery condition */
    public function where(string ...$condition): self
    {
        $this->where = array_merge($this->where, $condition);

        return $this;
    }

    /** Executes a COUNT() and returns the column */
    public function count(): int
    {
        $query = clone $this;
        $table = current($this->from);

        return $query->select("COUNT($table.id)")->execute()->fetchColumn();
    }

    /** Defines the query parameters */
    public function params(array $params): self
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    /** Specify which entity to use */
    public function into(string $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    /** Fetch a result */
    public function fetch(): mixed
    {
        $record = $this->execute()->fetch(PDO::FETCH_ASSOC);

        if ($record === false) {
            return false;
        }

        if ($this->entity) {
            return Hydrator::hydrate($record, $this->entity);
        }

        return $record;
    }

    /**
     * Returns a result or throw an exception
     *
     * @throws NoRecordException
     */
    public function fetchOrFail(): mixed
    {
        $record = $this->fetch();

        if ($record === false) {
            throw new NoRecordException();
        }

        return $record;
    }

    /** Execute the query */
    public function fetchAll(): QueryResult
    {
        return new QueryResult(
            $this->execute()->fetchAll(PDO::FETCH_ASSOC),
            $this->entity
        );
    }

    /** Paginate results */
    public function paginate(int $perPage, int $currentPage = 1): Pagerfanta
    {
        $paginator = new PaginatedQuery($this);

        return (new Pagerfanta($paginator))->setMaxPerPage($perPage)->setCurrentPage($currentPage);
    }

    /** Generate the SQL query */
    public function __toString(): string
    {
        $parts = ["SELECT"];

        if ($this->select) {
            $parts[] = join(', ', $this->select);
        } else {
            $parts[] = "*";
        }

        $parts[] = "FROM";
        $parts[] = $this->buildFrom();

        if (!empty($this->joins)) {
            foreach ($this->joins as $type => $joins) {
                foreach ($joins as [$table, $condition]) {
                    $parts[] = strtoupper($type) . " JOIN $table ON $condition";
                }
            }
        }

        if (!empty($this->where)) {
            $parts[] = "WHERE";
            $parts[] = "(" . join(") AND (", $this->where) . ")";
        }

        if (!empty($this->order)) {
            $parts[] = 'ORDER BY';
            $parts[] = join(', ', $this->order);
        }

        if ($this->limit) {
            $parts[] = 'LIMIT ' . $this->limit;
        }

        return join(' ', $parts);
    }

    /** Build FROM 'a' as 'b' */
    private function buildFrom(): string
    {
        $from = [];
        foreach ($this->from as $key => $value) {
            if (is_string($key)) {
                $from[] = "$key as $value";
            } else {
                $from[] = $value;
            }
        }

        return join(', ', $from);
    }

    /** Run the query */
    private function execute(): false|PDOStatement
    {
        $query = $this->__toString();

        if (!empty($this->params)) {
            $statement = $this->pdo->prepare($query);
            $statement->execute($this->params);

            return $statement;
        }

        return $this->pdo->query($query);
    }

    public function getIterator(): Traversable
    {
        return $this->fetchAll();
    }
}
