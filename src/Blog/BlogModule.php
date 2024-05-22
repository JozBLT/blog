<?php

namespace App\Blog;

use App\Blog\Actions\AdminBlogAction;
use App\Blog\Actions\BlogAction;
use Framework\Renderer\RendererInterface;
use Framework\Module;
use Framework\Router;
use Psr\Container\ContainerInterface;

class BlogModule extends Module
{
    const DEFINITIONS = __DIR__ . '/config.php';
    const MIGRATIONS  = __DIR__ . '/database/migrations';
    const SEEDS  = __DIR__ . '/database/seeds';

    public function __construct(ContainerInterface $container)
    {
        $container->get(RendererInterface::class)->addPath('blog', __DIR__ . '/' . 'views');
        $router = $container->get(Router::class);
        $router->get($container->get('blog.prefix'), BlogAction::class, 'blog.index');
        $router->get($container->get('blog.prefix') . '/[*:slug]-[i:id]', BlogAction::class, 'blog.show');

        if($container->has('admin.prefix')) {
            $prefix = $container->get('admin.prefix');
            $router->get("$prefix/posts", AdminBlogAction::class, 'admin.blog.index');
            $router->get("$prefix/posts/[i:id]", AdminBlogAction::class, 'admin.blog.edit');
            $router->post("$prefix/posts/[i:id]", AdminBlogAction::class, 'admin.blog.edit');
        };
    }
}
