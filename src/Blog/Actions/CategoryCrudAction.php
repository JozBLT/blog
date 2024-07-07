<?php

namespace App\Blog\Actions;

use App\Blog\Repository\CategoryRepository;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class CategoryCrudAction extends CrudAction
{

    protected /*string */$viewPath = "@blog/admin/categories";

    protected /*string */$routePrefix = "blog.category.admin";

    protected /*array */$acceptedParams = ['name', 'slug'];

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        CategoryRepository $repository,
        FlashService $flash
    ) {
        parent::__construct($renderer, $router, $repository, $flash);
    }

    protected function getValidator(ServerRequestInterface $request): Validator
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
