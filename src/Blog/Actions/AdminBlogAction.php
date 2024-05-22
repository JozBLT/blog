<?php

namespace App\Blog\Actions;

use App\Blog\Repository\PostRepository;
use Framework\Actions\RouterAwareAction;
use Framework\Router;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AdminBlogAction
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

    public function __invoke(Request $request)
    {
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
        return $this->index($request);
    }

    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->postRepository->findPaginated(12, $params['p'] ?? 1);
        return $this->renderer->render('@blog/admin/index', compact('items'));
    }

    /**
     * @param Request $request
     * @return Response|string
     */
    public function edit(Request $request): string|Response
    {
        $item = $this->postRepository->find($request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $params = array_filter($request->getParsedBody(), function ($key) {
                return in_array($key, ['name', 'slug', 'content']);
            }, ARRAY_FILTER_USE_KEY);
            var_dump($params);
            $this->postRepository->update($item->id, $params);
            return $this->redirect('admin.blog.index');
        }

        return $this->renderer->render('@blog/admin/edit', compact('item'));
    }
}
