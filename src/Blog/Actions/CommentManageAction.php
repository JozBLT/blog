<?php

namespace App\Blog\Actions;

use App\Comment\Repository\CommentRepository;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;

class CommentManageAction extends CrudAction
{

    protected string $viewPath = '@blog/admin/comments';

    protected string $routePrefix = 'blog.comments.admin';

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        CommentRepository $commentRepository,
        FlashService $flash
    ) {
        parent::__construct($renderer, $router, $commentRepository, $flash);
    }
}
