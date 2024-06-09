<?php

namespace App\Blog\Actions;

use App\Blog\Repository\CategoryRepository;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface as Request;

class CategoryCrudAction extends CrudAction
{
    protected ?string $viewPath = "@blog/admin/categories";

    protected ?string $routePrefix = "blog.category.admin";

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        CategoryRepository $repository,
        FlashService $flash
    ) {
        parent::__construct($renderer, $router, $repository, $flash);
    }

    protected function getParams(Request $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug']);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function getValidator(Request $request): Validator
    {
        return parent::getValidator($request)
            ->required('name', 'slug')
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->unique(
                'slug',
                $this->repository->getRepository(),
                $this->repository->getPdo(),
                $request->getAttribute('id')
            )
            ->slug('slug');
    }
}
