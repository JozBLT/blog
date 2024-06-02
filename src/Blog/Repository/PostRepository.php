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
        return parent::paginationQuery() . " ORDER BY created_at DESC";
    }
}
