<?php

namespace App\Blog\Actions;

use App\Blog\Repository\PostRepository;
use Exception;
use Framework\Actions\RouterAwareAction;
use Framework\Router;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogAction
{
    private RendererInterface $renderer;
    private PostRepository $postRepository;
    private Router $router;

    use RouterAwareAction;

    public function __construct(RendererInterface $renderer, Router $router, PostRepository $postRepository)
    {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postRepository = $postRepository;
    }

    /**
     * @throws Exception
     */
    public function __invoke(Request $request): string|ResponseInterface
    {
        if ($request->getAttribute('id')) {
            return $this->show($request);
        }
        return $this->index($request);
    }

    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $posts = $this->postRepository->findPaginated(12, $params['p'] ?? 1);
        return $this->renderer->render('@blog/index', compact('posts'));
    }

    /**
     * @throws Exception
     */
    public function show(Request $request): string|ResponseInterface
    {
        $slug = $request->getAttribute('slug');
        $post = $this->postRepository->find($request->getAttribute('id'));
        if ($post->slug !== $slug) {
            return $this->redirect('blog.show', [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }
        return $this->renderer->render('@blog/show', [
            'post' => $post
        ]);
    }
}
