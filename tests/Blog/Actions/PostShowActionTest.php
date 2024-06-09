<?php

namespace Tests\Blog\Actions;

use App\Blog\Actions\PostShowAction;
use App\Blog\Entity\Post;
use App\Blog\Repository\PostRepository;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class PostShowActionTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $rendererProphecy;

    private ObjectProphecy $postRepositoryProphecy;

    private ObjectProphecy $routerProphecy;

    private PostShowAction $action;

    public function setUp(): void
    {
        $this->rendererProphecy = $this->prophesize(RendererInterface::class);
        $this->postRepositoryProphecy = $this->prophesize(PostRepository::class);
        $this->routerProphecy = $this->prophesize(Router::class);

        $renderer = $this->rendererProphecy->reveal();
        $postRepository = $this->postRepositoryProphecy->reveal();
        $router = $this->routerProphecy->reveal();

        $this->action = new PostShowAction(
            $renderer,
            $router,
            $postRepository
        );
    }

    public function makePost(int $id, string $slug): Post
    {
        // Post
        $post = new Post();
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

        $this->routerProphecy->generateUri(
            'blog.show',
            ['id' => $post->id, 'slug' => $post->slug]
        )->willReturn('/demo2');
        $this->postRepositoryProphecy->findWithCategory($post->id)->willReturn($post);

        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals(['/demo2'], $response->getHeader('Location'));
    }

    public function testShowRender()
    {
        $post = $this->makePost(14, 'blabluuuu');
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', $post->slug);
        $this->postRepositoryProphecy->findWithCategory($post->id)->willReturn($post);
        $this->rendererProphecy->render('@blog/show', ['post' => $post])->willReturn('');
        $response = call_user_func_array($this->action, [$request]);
        $this->assertTrue(true);
    }

}
