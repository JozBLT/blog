<?php

namespace Framework\Actions;

use Exception;
use Framework\Database\Hydrator;
use Framework\Database\Repository;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CrudAction
{

    private RendererInterface $renderer;

    private Router $router;

    protected Repository $repository;

    private FlashService $flash;

    protected string $viewPath;

    protected string $routePrefix;

    /** @var string[] */
    protected array $messages = [
        'create' => "L'élément a bien été créé",
        'edit'   => "L'élément a bien été modifié",
        'validate' => "L'élément a bien été validé"
    ];

    protected array $acceptedParams = [];

    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        Repository $repository,
        FlashService $flash
    ) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->repository = $repository;
        $this->flash = $flash;
    }

    /** @throws Exception */
    public function __invoke(ServerRequestInterface $request): string|ResponseInterface
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);

        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (str_ends_with((string)$request->getUri(), 'new')) {
            return $this->create($request);
        }
        if (str_ends_with((string)$request->getUri(), 'validate')) {
            return $this->validate($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }

        return $this->index($request);
    }

    /** Display elements list */
    public function index(ServerRequestInterface $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->repository->findAll()->paginate(12, $params['p'] ?? 1);

        return $this->renderer->render($this->viewPath . '/index', compact('items'));
    }

    /** @throws Exception */
    public function validate(ServerRequestInterface $request): ResponseInterface
    {
        $this->repository->validate($request->getAttribute('id'), 'published');
        $this->flash->success($this->messages['validate']);

        return $this->redirect($this->routePrefix . '.index');
    }

    /** @throws Exception */
    public function edit(ServerRequestInterface $request): string|ResponseInterface
    {
        $errors = '';
        $item = $this->repository->find($request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);

            if ($validator->isValid()) {
                $this->repository->update($item->id, $this->getParams($request, $item));
                $this->flash->success($this->messages['edit']);

                return $this->redirect($this->routePrefix . '.index');
            }

            $errors = $validator->getErrors();
            Hydrator::hydrate($request->getParsedBody(), $item);
        }

        return $this->renderer->render(
            $this->viewPath . '/edit',
            $this->formParams(compact('item', 'errors'))
        );
    }

    /** @throws Exception */
    public function create(ServerRequestInterface $request): string|ResponseInterface
    {
        $errors = '';
        $item = $this->getNewEntity();

        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);

            if ($validator->isValid()) {
                $this->repository->insert($this->getParams($request, $item));
                $this->flash->success($this->messages['create']);
                return $this->redirect($this->routePrefix . '.index');
            }
            Hydrator::hydrate($request->getParsedBody(), $item);
            $errors = $validator->getErrors();
        }

        return $this->renderer->render(
            $this->viewPath . '/create',
            $this->formParams(compact('item', 'errors'))
        );
    }

    /** @throws Exception */
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $this->repository->delete($request->getAttribute('id'));

        return $this->redirect($this->routePrefix . '.index');
    }

    /** Filters the parameters received by the request */
    protected function getParams(ServerRequestInterface $request, object $item): array
    {
        return array_filter(array_merge($request->getParsedBody(), $request->getUploadedFiles()), function ($key) {
            return in_array($key, $this->acceptedParams);
        }, ARRAY_FILTER_USE_KEY);
    }

    /** Generates a validator for data validation */
    protected function getValidator(ServerRequestInterface $request): Validator
    {
        return new Validator(array_merge($request->getParsedBody(), $request->getUploadedFiles()));
    }

    /** Generates a new entity for the 'create' action */
    protected function getNewEntity(): mixed
    {
        $entity = $this->repository->getEntity();
        return new $entity();
    }

    /** Processes parameters to send to the view */
    protected function formParams(array $params): array
    {
        return $params;
    }
}
