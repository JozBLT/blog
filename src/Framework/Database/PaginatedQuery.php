<?php

namespace Framework\Database;

use Pagerfanta\Adapter\AdapterInterface;
use PDO;

class PaginatedQuery implements AdapterInterface
{
    private PDO $pdo;
    private string $query;
    private string $countQuery;
    private string|null $entity;
    private array $params;

    public function __construct(
        PDO $pdo,
        string $query,
        string $countQuery,
        ?string $entity,
        array $params = []
    ) {

        $this->pdo = $pdo;
        $this->query = $query;
        $this->countQuery = $countQuery;
        $this->entity = $entity;
        $this->params = $params;
    }

    /** Returns the number of results */
    public function getNbResults(): int
    {
        if (!empty($this->params)) {
            $query = $this->pdo->prepare($this->countQuery);
            $query->execute($this->params);

            return $query->fetchColumn();
        }

        return $this->pdo->query($this->countQuery)->fetchColumn();
    }

    /** Returns a slice of the results */
    public function getSlice(int $offset, int $length): iterable
    {
        $statement = $this->pdo->prepare($this->query . ' LIMIT :offset, :length');

        foreach ($this->params as $key => $param) {
            $statement->bindParam($key, $param);
        }

        $statement->bindParam('offset', $offset, PDO::PARAM_INT);
        $statement->bindParam('length', $length, PDO::PARAM_INT);

        if ($this->entity) {
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
        $statement->execute();

        return $statement->fetchAll();
    }
}
