<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\Repository\CategoryRepository;
use App\Blog\Repository\PostRepository;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostCrudAction extends CrudAction
{
    protected ?string $viewPath = "@blog/admin/posts";

    protected ?string $routePrefix = "blog.admin";

    private CategoryRepository $categoryRepository;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostRepository $repository,
        FlashService $flash,
        CategoryRepository $categoryRepository
    ) {
        parent::__construct($renderer, $router, $repository, $flash);
        $this->categoryRepository = $categoryRepository;
    }

    protected function formParams(array $params): array
    {
        $params['categories'] = $this->categoryRepository->findList();
        return $params;
    }

    protected function getNewEntity(): Post
    {
        $post = new Post();
        $post->created_at = new \DateTime();
        return $post;
    }

    protected function getParams(Request $request): object|array|null
    {
        $params = array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug', 'content', 'created_at', 'category_id']);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, [
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    protected function getValidator(Request $request): Validator
    {
        return parent::getValidator($request)
            ->required('content', 'name', 'slug', 'created_at', 'category_id')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->exists(
                'category_id',
                $this->categoryRepository->getRepository(),
                $this->categoryRepository->getPdo()
            )
            ->dateTime('created_at')
            ->slug('slug');
    }
}
