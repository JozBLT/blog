<?php

namespace App\Blog\Actions;

use App\Blog\Repository\PostRepository;
use Exception;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostShowAction
{

    private RendererInterface $renderer;

    private PostRepository $postRepository;

    private Router $router;

    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostRepository $postRepository
    ) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postRepository = $postRepository;
    }

    /**
     * Display an article
     *
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $request): string|ResponseInterface
    {
        $slug = $request->getAttribute('slug');
        $post = $this->postRepository->findWithCategory($request->getAttribute('id'));

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
