<?php

namespace App\Blog;

use App\Blog\Actions\CategoryCrudAction;
use App\Blog\Actions\CategoryShowAction;
use App\Blog\Actions\CommentManageAction;
use App\Blog\Actions\HomePageAction;
use App\Blog\Actions\PostCrudAction;
use App\Blog\Actions\PostIndexAction;
use App\Blog\Actions\PostShowAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class BlogModule extends Module
{

    const DEFINITIONS = __DIR__ . '/config.php';

    const MIGRATIONS  = __DIR__ . '/Database/migrations';

    const SEEDS  = __DIR__ . '/Database/seeds';

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $blogPrefix = $container->get('blog.prefix');
        $container->get(RendererInterface::class)->addPath('blog', __DIR__ . '/views');
        $router = $container->get(Router::class);
        \assert($router instanceof Router);
        $router->get('/', HomePageAction::class, 'homepage');
        $router->post('/', HomePageAction::class, 'homepage.contact');
        $router->get($blogPrefix, PostIndexAction::class, 'blog.index');
        $router->get("$blogPrefix/{slug:[a-z\-0-9]+}-{id:[0-9]+}", PostShowAction::class, 'blog.show');
        $router->get("$blogPrefix/category/{slug:[a-z\-0-9]+}", CategoryShowAction::class, 'blog.category');

        if ($container->has('admin.prefix')) {
            $prefix = $container->get('admin.prefix');
            $router->crud("$prefix/posts", PostCrudAction::class, 'blog.posts.admin');
            $router->crud("$prefix/categories", CategoryCrudAction::class, 'blog.category.admin');
            $router->crud("$prefix/comments", CommentManageAction::class, 'blog.comments.admin');
            $router->post(
                "$prefix/comments/{id:[0-9]+}/validate",
                [CommentManageAction::class, 'validate'],
                'blog.comments.admin.validate'
            );
        }
    }
}
