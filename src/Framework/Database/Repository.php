<?php

namespace Framework\Database;

use Pagerfanta\Pagerfanta;

class Repository
{

    private \PDO $pdo;

    /**
     * Nom de la table en BDD
     */
    protected string $repository;

    /**
     * Entité à utiliser
     */
    protected ?string $entity = null;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Order elements
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query =  new PaginatedQuery(
            $this->pdo,
            $this->paginationQuery(),
            'SELECT COUNT(id) FROM posts',
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

    /**
     * Find an element with his id
     */
    public function find(int $id): mixed
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->repository} WHERE id = ?");
        $query->execute([$id]);
        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        return $query->fetch();
    }

    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildFieldQuery($params);
        $params["id"] = $id;
        $statement = $this->pdo->prepare("UPDATE {$this->repository} SET $fieldQuery WHERE id = :id");
        return $statement->execute($params);
    }

    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        $values = join(', ', array_map(function ($field) {
            return ':' . $field;
        }, $fields));
        $fields = join(', ', $fields);
        $statement = $this->pdo->prepare("INSERT INTO {$this->repository} ($fields) VALUES ($values)");
        return $statement->execute($params);
    }

    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare("DELETE FROM {$this->repository} WHERE id = ?");
        return $statement->execute([$id]);
    }

    private function buildFieldQuery(array $params): string
    {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    public function getRepository(): string
    {
        return $this->repository;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }
}
