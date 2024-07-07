<?php

namespace App\Blog\Repository;

use App\Blog\Entity\Post;
use Framework\Database\Query;
use Framework\Database\Repository;

class PostRepository extends Repository
{

    protected /*?string */$entity = Post::class;

    protected /*string */$repository = 'posts';

    public function findAll(): Query
    {
        $category = new CategoryRepository($this->pdo);
        return $this->makeQuery()
            ->join($category->getRepository() . ' as c', 'c.id = p.category_id')
            ->select('p.*, c.name as category_name, c.slug as category_slug')
            ->order('p.created_at DESC');
    }

    public function findPublic(): Query
    {
        return $this->findAll()
            ->where('p.published = 1')
            ->where('p.created_at < NOW()');
    }

    public function findPublicForCategory(int $id): Query
    {
        return $this->findPublic()->where("p.category_id = $id");
    }

    public function findWithCategory(int $postId): Post
    {
        return $this->findPublic()->where("p.id = $postId")->fetch();
    }
}
