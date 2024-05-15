<?php

namespace Tests\Framework\Blog\Actions;

use App\Blog\Actions\BlogAction;
use App\Blog\Repository\PostRepository;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class BlogActionTest extends TestCase
{
    use ProphecyTrait;

    private RendererInterface $renderer;
    private Router $router;
    private PostRepository $postRepository;
    private BlogAction $action;

    public function setUp(): void
    {
        $this->renderer = $this->prophesize(RendererInterface::class)->reveal();
        $this->router = $this->prophesize(Router::class)->reveal();
        $this->postRepository = $this->prophesize(PostRepository::class)->reveal();
        $this->action = new BlogAction(
            $this->renderer,
            $this->router,
            $this->postRepository,
        );
    }

    public function makePost(int $id, string $slug): \stdClass
    {
        // Post
        $post = new \stdClass();
        $post->id = $id;
        $post->slug = $slug;
        return $post;
    }

    public function testShowRedirect()
    {
        $post = $this->makePost(14, 'blabluuuu');
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', 'demo');

        $this->router->generateUri(
            'blog.show',
            ['id' => $post->id, 'slug' => $post->slug]
        )->willReturn('/demo2');
        $this->postRepository->find($post->id)->willReturn($post);

        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals(['/demo2'], $response->getHeader('location'));
    }

    public function testShowRender()
    {
        $post = $this->makePost(14, 'blabluuuu');
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', $post->slug);
        $this->postRepository->find($post->id)->willReturn($post);
        $this->renderer->render('@blog/show', ['post' => $post])->willReturn('');
        $response = call_user_func_array($this->action, [$request]);
        $this->assertTrue(true);
    }

}