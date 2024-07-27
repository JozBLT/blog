<?php

namespace App\Comment\Actions;

use App\Blog\Repository\PostRepository;
use App\Comment\Repository\CommentRepository;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class CommentAction
{

    private RendererInterface $renderer;

    private Router $router;

    private PostRepository $postRepository;

    private CommentRepository $commentRepository;

    private FlashService $flashService;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostRepository $postRepository,
        CommentRepository $commentRepository,
        FlashService $flashService
    ) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
        $this->flashService = $flashService;
    }

    public function __invoke(ServerRequestInterface $request): string|RedirectResponse
    {
        $params = $request->getParsedBody();
        $postId = $request->getAttribute('id');
        $slug = $request->getAttribute('slug');
        $post = $this->postRepository->findWithCategory($postId);

        $validator = (new Validator($params))
            ->required('username', 'comment')
            ->length('username', 3)
            ->length('comment', 10, 5000);

        if ($validator->isValid()) {
            $recentComments = $this->commentRepository->findRecentByPostAndUser($postId, $params['username']);
            $recentCommentsArray = iterator_to_array($recentComments);

            if (count($recentCommentsArray) > 0) {
                $this->flashService->error('Vous ne pouvez poster qu\'un commentaire toutes les 15 minutes.');

                return new RedirectResponse($this->router->generateUri('blog.show', [
                    'slug' => $slug,
                    'id' => $postId
                ]));
            }

            $this->commentRepository->insert([
                'post_id' => $postId,
                'username' => $params['username'],
                'comment' => $params['comment'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $this->flashService
                ->success('Votre commentaire a été posté. Il sera visible une fois validé par un administrateur');

            return new RedirectResponse($this->router->generateUri('blog.show', [
                'slug' => $slug,
                'id' => $postId
            ]));
        }

        $errors = $validator->getErrors();

        return $this->renderer->render('@blog/show', [
            'post' => $post,
            'errors' => $errors,
            'old' => $params
        ]);
    }
}
