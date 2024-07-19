<?php

namespace App\Blog\Actions;

use App\Blog\Repository\CategoryRepository;
use App\Blog\Repository\PostRepository;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostIndexAction
{

    private RendererInterface $renderer;

    private PostRepository $postRepository;

    private CategoryRepository $categoryRepository;

    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        PostRepository $postRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->renderer = $renderer;
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function __invoke(ServerRequestInterface $request): string
    {
        $params = $request->getQueryParams();
        $posts = $this->postRepository->findPublic()->paginate(12, $params['p'] ?? 1);
        $categories = $this->categoryRepository->findAll();

        return $this->renderer->render('@blog/index', compact('posts', 'categories'));
    }
}
