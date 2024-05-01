<?php

namespace App\Blog;

use App\Blog\Actions\BlogAction;
use Framework\Renderer\RendererInterface;
use Framework\Module;
use Framework\Router;

class BlogModule extends Module
{
    const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(string $prefix, Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('blog', __DIR__ . '/' . 'views');
        $router->get($prefix, BlogAction::class, 'blog.index');
        $router->get($prefix . '/[*:slug]', BlogAction::class, 'blog.show');
    }
}
