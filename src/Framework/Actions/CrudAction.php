<?php

namespace Framework\Actions;

use App\Blog\Entity\Post;
use Exception;
use App\Blog\Repository\PostRepository;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CrudAction
{
    private RendererInterface $renderer;
    private mixed $repository;
    private Router $router;
    private FlashService $flash;

    protected ?string $viewPath;

    protected ?string $routePrefix;

    /**
     * @var string[]
     */
    protected array $messages = [
        'create' => "L'élément a bien été créé",
        'edit' => "L'élément a bien été modifié"
    ];

    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        mixed $repository,
        FlashService $flash
    ) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->repository = $repository;
        $this->flash = $flash;
    }

    /**
     * @throws Exception
     */
    public function __invoke(Request $request): string|Response
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);
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
     * Display elements list
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->repository->findPaginated(12, $params['p'] ?? 1);
        return $this->renderer->render($this->viewPath . '/index', compact('items'));
    }

    /**
     * @throws Exception
     */
    public function edit(Request $request): string|Response
    {
        $errors = '';
        $item = $this->repository->find($request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->repository->update($item->id, $params);
                $this->flash->success($this->messages['edit']);
                return $this->redirect($this->routePrefix . '.index');
            }
            $errors = $validator->getErrors();
            $params['id'] = $item->id;
            $item = $params;
        }
        return $this->renderer->render($this->viewPath . '/edit', compact('item', 'errors'));
    }

    /**
     * @throws Exception
     */
    public function create(Request $request): string|Response
    {
        $errors = '';
        $item = $this->getNewEntity();
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->repository->insert($params);
                $this->flash->success($this->messages['create']);
                return $this->redirect($this->routePrefix . '.index');
            }
            $errors = $validator->getErrors();
            $item = $params;
        }
        return $this->renderer->render($this->viewPath . '/create', compact('item', 'errors'));
    }

    /**
     * @throws Exception
     */
    public function delete(Request $request): Response
    {
        $this->repository->delete($request->getAttribute('id'));
        return $this->redirect($this->routePrefix . '.index');
    }

    protected function getParams(Request $request): object|array|null
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, []);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function getValidator(Request $request): Validator
    {
        return new Validator($request->getParsedBody());
    }

    protected function getNewEntity()
    {
        return [];
    }
}
