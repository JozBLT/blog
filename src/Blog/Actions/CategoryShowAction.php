<?php

namespace App\Blog\Actions;

use App\Blog\Repository\CategoryRepository;
use App\Blog\Repository\PostRepository;
use Framework\Actions\RouterAwareAction;
use Framework\Database\NoRecordException;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class CategoryShowAction
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

    /** @throws NoRecordException */
    public function __invoke(ServerRequestInterface $request): string
    {
        $params = $request->getQueryParams();
        $category = $this->categoryRepository->findBy('slug', $request->getAttribute('slug'));
        $posts = $this->postRepository->findPublicForCategory($category->id)->paginate(12, $params['p'] ?? 1);
        $categories = $this->categoryRepository->findAll();
        $page = $params['p'] ?? 1;

        return $this->renderer->render('@blog/index', compact('posts', 'categories', 'category', 'page'));
    }
}
