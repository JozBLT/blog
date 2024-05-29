<?php

namespace App\Blog\Actions;

use App\Blog\Repository\PostRepository;
use Exception;
use Framework\Actions\RouterAwareAction;
use Framework\Router;
use Framework\Renderer\RendererInterface;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AdminBlogAction
{
    private RendererInterface $renderer;
    private PostRepository $postRepository;
    private Router $router;
    private FlashService $flash;

    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostRepository $postRepository,
        FlashService $flash
    ){
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postRepository = $postRepository;
        $this->flash = $flash;
    }

    public function __invoke(Request $request): string|Response
    {
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (str_ends_with((string)$request->getUri(), 'new')) {
            return $this->create($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
        return $this->index($request);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->postRepository->findPaginated(12, $params['p'] ?? 1);
        return $this->renderer->render('@blog/admin/index', compact('items'));
    }

    /**
     * @param Request $request
     * @return Response|string
     * @throws Exception
     */
    public function edit(Request $request): string|Response
    {
        $item = $this->postRepository->find($request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $params['updated_at'] = date('Y-m-d H:i:s');
            $this->postRepository->update($item->id, $params);
            $this->flash->success('L\'article a bien été modifié');
            return $this->redirect('blog.admin.index');
        }
        return $this->renderer->render('@blog/admin/edit', compact('item'));
    }

    /**
     * @param Request $request
     * @return string|Response
     * @throws Exception
     */
    public function create(Request $request): string|Response
    {
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $params = array_merge($params, [
                'updated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $this->postRepository->insert($params);
            $this->flash->success('L\'article a bien été créé');
            return $this->redirect('blog.admin.index');
        }
        return $this->renderer->render('@blog/admin/create'/*  , compact('item')  */);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request): Response
    {
        $this->postRepository->delete($request->getAttribute('id'));
        return $this->redirect('blog.admin.index');
    }

    /**
     * @param Request $request
     * @return array|object|null
     */
    private function getParams(Request $request): object|array|null
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug', 'content']);
        }, ARRAY_FILTER_USE_KEY);
    }
}
