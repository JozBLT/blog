<?php

namespace Framework\Database;

use App\Auth\User;
use PDO;
use stdClass;

class Repository
{

    protected null|PDO $pdo;

    /** Table name in database */
    protected string $repository;

    /** Entity to use */
    protected string $entity = stdClass::class;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Find a key/value list of elements */
    public function findList(): array
    {
        $results = $this->pdo
            ->query("SELECT id, name FROM $this->repository")
            ->fetchAll(PDO::FETCH_NUM);
        $list = [];

        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }

        return $list;
    }

    protected function makeQuery(): Query
    {
        return (new Query($this->pdo))
            ->from($this->repository, $this->repository[0])
            ->into($this->entity);
    }

    /** Retrieve all records */
    public function findAll(): Query
    {
        return $this->makeQuery();
    }

    /**
     * Retrieve a row relative to a field
     *
     * @throws NoRecordException
     */
    public function findBy(string $field, string $value): array|stdClass|User
    {
        return $this->makeQuery()->where("$field = :field")->params(["field" => $value])->fetchOrFail();
    }

    /**
     * Find an element with his id
     *
     * @throws NoRecordException
     */
    public function find(int $id): mixed
    {
        return $this->makeQuery()->where("id = $id")->fetchOrFail();
    }

    /** Get the number of records */
    public function count(): int
    {
        return $this->makeQuery()->count();
    }

    /** Update a record in DB */
    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildFieldQuery($params);
        $params["id"] = $id;
        $query = $this->pdo->prepare("UPDATE $this->repository SET $fieldQuery WHERE id = :id");

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
        $query = $this->pdo->prepare("INSERT INTO $this->repository ($fields) VALUES ($values)");

        return $query->execute($params);
    }

    /** Delete a record */
    public function delete(int $id): bool
    {
        $query = $this->pdo->prepare("DELETE FROM $this->repository WHERE id = ?");

        return $query->execute([$id]);
    }

    private function buildFieldQuery(array $params): string
    {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function getRepository(): string
    {
        return $this->repository;
    }

    /** Check if an element exists */
    public function exists(int $id): bool
    {
        $query = $this->pdo->prepare("SELECT id FROM $this->repository WHERE id = ?");
        $query->execute([$id]);

        return $query->fetchColumn() !== false;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
