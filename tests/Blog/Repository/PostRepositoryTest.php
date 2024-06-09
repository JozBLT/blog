<?php

namespace Tests\Blog\Repository;

use App\Blog\Entity\Post;
use App\Blog\Repository\PostRepository;
use Framework\Database\NoRecordException;
use Tests\DatabaseTestCase;

class PostRepositoryTest extends DatabaseTestCase
{
    private PostRepository $postRepository;

    public function setUp(): void
    {
        parent::setUp();
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->postRepository = new PostRepository($pdo);
    }

    /** @throws NoRecordException */
    public function testFind()
    {
        $this->seedDatabase($this->postRepository->getPdo());
        $post = $this->postRepository->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testFindRecordNotFound()
    {
        $this->expectException(NoRecordException::class);
        $this->postRepository->find(1);
    }

    /** @throws NoRecordException */
    public function testUpdate()
    {
        $this->seedDatabase($this->postRepository->getPdo());
        $this->postRepository->update(1, ['name' => 'Salut', 'slug' => 'demo']);
        $post = $this->postRepository->find(1);
        $this->assertEquals('Salut', $post->name);
        $this->assertEquals('demo', $post->slug);
    }

    /** @throws NoRecordException */
    public function testInsert()
    {
        $this->postRepository->insert(['name' => 'Salut', 'slug' => 'demo']);
        $post = $this->postRepository->find(1);
        $this->assertEquals('Salut', $post->name);
        $this->assertEquals('demo', $post->slug);
    }

    public function testDelete()
    {
        $this->postRepository->insert(['name' => 'Salut', 'slug' => 'demo']);
        $this->postRepository->insert(['name' => 'Salut', 'slug' => 'demo']);
        $count = $this->postRepository->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(2, (int)$count);
        $this->postRepository->delete($this->postRepository->getPdo()->lastInsertId());
        $count = $this->postRepository->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(1, (int)$count);
    }

}
