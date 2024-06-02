<?php

namespace App\Blog;

use App\Blog\Actions\PostCrudAction;
use App\Blog\Actions\BlogAction;
use Framework\Renderer\RendererInterface;
use Framework\Module;
use Framework\Router;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class BlogModule extends Module
{
    const DEFINITIONS = __DIR__ . '/config.php';
    const MIGRATIONS  = __DIR__ . '/database/migrations';
    const SEEDS  = __DIR__ . '/database/seeds';

    /**
     * @param ContainerInterface $container
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $blogPrefix = $container->get('blog.prefix');
        $container->get(RendererInterface::class)->addPath('blog', __DIR__ . '/' . 'views');
        $router = $container->get(Router::class);
        $router->get($blogPrefix, BlogAction::class, 'blog.index');
        $router->get("$blogPrefix/[*:slug]-[i:id]", BlogAction::class, 'blog.show');

        if ($container->has('admin.prefix')) {
            $prefix = $container->get('admin.prefix');
            $router->crud("$prefix/posts", PostCrudAction::class, 'blog.admin');
        };
    }
}
