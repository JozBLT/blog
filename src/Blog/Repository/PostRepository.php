<?php

namespace App\Blog\Repository;

use App\Blog\Entity\Post;
use Framework\Database\Repository;

class PostRepository extends Repository
{
    protected ?string $entity = Post::class;

    protected string $repository = 'posts';

    protected function paginationQuery(): string
    {
        return "SELECT posts.id, posts.name, categories.name category_name
        FROM {$this->repository}
        LEFT JOIN categories ON posts.category_id = categories.id
        ORDER BY created_at DESC";
    }
}
