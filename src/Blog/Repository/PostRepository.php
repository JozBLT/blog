<?php

namespace App\Blog\Repository;

use App\Blog\Entity\Post;
use Framework\Database\NoRecordException;
use Framework\Database\PaginatedQuery;
use Framework\Database\Repository;
use Pagerfanta\Pagerfanta;

class PostRepository extends Repository
{
    protected ?string $entity = Post::class;

    protected string $repository = 'posts';

    public function findPaginatedPublic(int $perPage, int $currentPage): Pagerfanta
    {
        $query =  new PaginatedQuery(
            $this->getPdo(),
            "SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM posts as p
            LEFT JOIN categories as c ON c.id = p.category_id
            ORDER BY p.created_at DESC",
            "SELECT COUNT(id) FROM {$this->repository}",
            $this->entity
        );

        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findPaginatedPublicForCategory(int $perPage, int $currentPage, int $categoryId): Pagerfanta
    {
        $query =  new PaginatedQuery(
            $this->getPdo(),
            "SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM posts as p
            LEFT JOIN categories as c ON c.id = p.category_id
            WHERE p.category_id = :category
            ORDER BY p.created_at DESC",
            "SELECT COUNT(id) FROM {$this->repository} WHERE category_id = :category",
            $this->entity,
            ['category' => $categoryId]
        );

        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findWithCategory(int $id)
    {
        return $this->fetchOrFail("
        SELECT p.*, c.name category_name, c.slug category_slug
        FROM posts as p
        LEFT JOIN categories as c ON c.id = p.category_id
        WHERE p.id = ?
        ", [$id]);
    }

    protected function paginationQuery(): string
    {
        return "SELECT posts.id, posts.name, categories.name category_name
        FROM {$this->repository}
        LEFT JOIN categories ON posts.category_id = categories.id
        ORDER BY created_at DESC";
    }
}
