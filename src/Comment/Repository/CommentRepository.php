<?php

namespace App\Comment\Repository;

use App\Blog\Repository\PostRepository;
use App\Comment\Entity\Comment;
use Framework\Database\Query;
use Framework\Database\QueryResult;
use Framework\Database\Repository;

class CommentRepository extends Repository
{

    protected string $entity = Comment::class;

    protected string $repository = 'comments';

    public function findAll(): Query
    {
        $post = new PostRepository($this->pdo);
        return $this->makeQuery()
            ->join($post->getRepository() . ' as p', 'p.id = c.post_id')
            ->select('c.*, p.name as post_name')
            ->order('c.created_at DESC');
    }

    /** Find the last comment sent by the user on the post in the last 15min */
    public function findRecentByPostAndUser(int $postId, string $username, int $minutes = 15): QueryResult
    {
        return $this->makeQuery()
            ->from($this->repository, 'c')
            ->where("c.post_id = :post_id")
            ->where("c.username = :username")
            ->where("c.created_at >= NOW() - INTERVAL $minutes MINUTE")
            ->params([
                'post_id' => $postId,
                'username' => $username
            ])
            ->fetchAll();
    }

    /** Find all published comments on a post */
    public function findPublishedByPost(int $postId): QueryResult
    {
        return $this->makeQuery()
            ->from($this->repository, 'c')
            ->where('c.post_id = :post_id')
            ->where('c.published = 1')
            ->params([
                'post_id' => $postId
            ])
            ->fetchAll();
    }
}
