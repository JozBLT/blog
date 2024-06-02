<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
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

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostRepository $repository,
        FlashService $flash
    ) {
        parent::__construct($renderer, $router, $repository, $flash);
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
            return in_array($key, ['name', 'slug', 'content', 'created_at']);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, [
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    protected function getValidator(Request $request): Validator
    {
        return parent::getValidator($request)
            ->required('content', 'name', 'slug', 'created_at')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->dateTime('created_at')
            ->slug('slug');
    }
}
