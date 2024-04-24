<?php

namespace App\Blog;

use Framework\Router;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogModule
{

    private RendererInterface $renderer;

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->renderer = $renderer;
        $this->renderer->addPath('blog', __DIR__ . '/' . 'views');
        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get('/blog/[*:slug]', [$this, 'show'], 'blog.show');
    }

    public function index(Request $request): string //Response
    {
        return $this->renderer->render('@blog/index');

//        $content = $this->renderer->render('@blog/index');
//        return new \GuzzleHttp\Psr7\Response(200, [], $content);
    }

    public function show(Request $request): string //Response
    {
        return $this->renderer->render('@blog/show', [
            'slug' => $request->getAttribute('slug')
        ]);

//        $content = $this->renderer->render('@blog/show', [
//            'slug' => $request->getAttribute('slug')
//        ]);
//        return new \GuzzleHttp\Psr7\Response(200, [], $content);
    }
}
