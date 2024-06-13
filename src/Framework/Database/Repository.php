<?php

namespace Framework\Database;

use Pagerfanta\Pagerfanta;
use PDO;

class Repository
{

    private PDO $pdo;

    /** Nom de la table en BDD */
    protected string $repository;

    /** Entité à utiliser */
    protected ?string $entity = null;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Order elements */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query =  new PaginatedQuery(
            $this->pdo,
            $this->paginationQuery(),
            "SELECT COUNT(id) FROM {$this->repository}",
            $this->entity
        );

        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    protected function paginationQuery(): string
    {
        return "SELECT * FROM {$this->repository}";
    }

    /** Find a key/value list of elements */
    public function findList(): array
    {
        $results = $this->pdo
            ->query("SELECT id, name FROM {$this->repository}")
            ->fetchAll(PDO::FETCH_NUM);
        $list = [];

        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }

        return $list;
    }

    /** Retrieve all records */
    public function findAll(): array
    {
        $query = $this->pdo->query("SELECT * FROM {$this->repository}");

        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        } else {
            $query->setFetchMode(PDO::FETCH_OBJ);
        }

        return $query->fetchAll();
    }

    /**
     * Retrieve a row relative to a field
     *
     * @throws NoRecordException
     */
    public function findBy(string $field, string $value): mixed
    {
        return $this->fetchOrFail("SELECT * FROM {$this->repository} WHERE $field = ?", [$value]);
    }

    /**
     * Find an element with his id
     *
     * @throws NoRecordException
     */
    public function find(int $id): mixed
    {
        return $this->fetchOrFail("SELECT * FROM {$this->repository} WHERE id = ?", [$id]);
    }

    /** Get the number of records */
    public function count(): int
    {
        return $this->fetchColumn("SELECT COUNT(id) FROM {$this->repository}");
    }

    /** Update a record in DB */
    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildFieldQuery($params);
        $params["id"] = $id;
        $query = $this->pdo->prepare("UPDATE {$this->repository} SET $fieldQuery WHERE id = :id");

        return $query->execute($params);
    }

    /** Insert a new record in DB */
    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        $values = join(', ', array_map(function ($field) {
            return ':' . $field;
        }, $fields));
        $fields = join(', ', $fields);
        $query = $this->pdo->prepare("INSERT INTO {$this->repository} ($fields) VALUES ($values)");

        return $query->execute($params);
    }

    /** Delete a record */
    public function delete(int $id): bool
    {
        $query = $this->pdo->prepare("DELETE FROM {$this->repository} WHERE id = ?");

        return $query->execute([$id]);
    }

    private function buildFieldQuery(array $params): string
    {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }

    /** @return mixed */
    public function getEntity(): string
    {
        return $this->entity;
    }

    public function getRepository(): string
    {
        return $this->repository;
    }

    /** Check if an element exists */
    public function exists($id): bool
    {
        $query = $this->pdo->prepare("SELECT id FROM {$this->repository} WHERE id = ?");
        $query->execute([$id]);

        return $query->fetchColumn() !== false;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Executes a query and retrieves the first result
     *
     * @throws NoRecordException
     */
    protected function fetchOrFail(string $query, array $params = []): mixed
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);

        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }

        $record = $query->fetch();

        if ($record === false) {
            throw new NoRecordException();
        }

        return $record;
    }

    /** Fetch first column */
    private function fetchColumn(string $query, array $params = []): mixed
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);

        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }

        return $query->fetchColumn();
    }
}
