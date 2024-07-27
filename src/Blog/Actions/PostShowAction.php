<?php

namespace App\Blog\Actions;

use App\Blog\Repository\PostRepository;
use App\Comment\Repository\CommentRepository;
use Exception;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostShowAction
{

    private RendererInterface $renderer;

    private Router $router;

    private PostRepository $postRepository;

    private CommentRepository $commentRepository;

    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostRepository $postRepository,
        CommentRepository $commentRepository
    ) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * Display an article and his comments if they're published
     *
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $request): string|ResponseInterface
    {
        $slug = $request->getAttribute('slug');
        $postId = $request->getAttribute('id');
        $post = $this->postRepository->findWithCategory($postId);
        $comments = $this->commentRepository->findPublishedByPost($postId);

        if ($post->slug !== $slug) {
            return $this->redirect('blog.show', [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }

        return $this->renderer->render('@blog/show', [
            'post' => $post,
            'comments' => $comments
        ]);
    }
}
