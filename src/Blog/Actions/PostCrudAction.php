<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\PostUpload;
use App\Blog\Repository\CategoryRepository;
use App\Blog\Repository\PostRepository;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostCrudAction extends CrudAction
{
    protected ?string $viewPath = "@blog/admin/posts";

    protected ?string $routePrefix = "blog.admin";

    private CategoryRepository $categoryRepository;
    private PostUpload $postUpload;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostRepository $repository,
        FlashService $flash,
        CategoryRepository $categoryRepository,
        PostUpload $postUpload
    ) {
        parent::__construct($renderer, $router, $repository, $flash);
        $this->categoryRepository = $categoryRepository;
        $this->postUpload = $postUpload;
    }

    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $post = $this->repository->find($request->getAttribute('id'));
        $this->postUpload->delete($post->image);

        return parent::delete($request);
    }

    protected function formParams(array $params): array
    {
        $params['categories'] = $this->categoryRepository->findList();

        return $params;
    }

    /** @return Post[] */
    protected function getNewEntity(): array
    {
        $post = new Post();
        $post->created_at = new \DateTime();

        return [$post];
    }

    /** @param Post $post */
    protected function getParams(Request $request, $post): array
    {
        $params = array_merge($request->getParsedBody(), $request->getUploadedFiles());
        $image = $this->postUpload->upload($params['image'], $post->image);

        if ($image) {
            $params['image'] = $image;
        } else {
            unset($params['image']);
        }

        $params = array_filter($params, function ($key) {
            return in_array($key, ['name', 'slug', 'content', 'created_at', 'category_id', 'image', 'published']);
        }, ARRAY_FILTER_USE_KEY);

        return array_merge($params, ['updated_at' => date('Y-m-d H:i:s')]);
    }

    protected function getValidator(Request $request): Validator
    {
        $validator = parent::getValidator($request)
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
            ->extension('image', ['jpg', 'png'])
            ->slug('slug');

        if (is_null($request->getAttribute('id'))) {
            $validator->uploaded('image');
        }

        return $validator;
    }
}
