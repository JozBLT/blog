<?php

namespace App\Blog\Actions;

use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogAction
{
    private RendererInterface $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request)
    {
        $slug = $request->getAttribute('slug');
        if ($slug) {
            return $this->show($slug);
        }
        return $this->index();
    }

    public function index(): string //Response
    {
        return $this->renderer->render('@blog/index');

//        $content = $this->renderer->render('@blog/index');
//        return new \GuzzleHttp\Psr7\Response(200, [], $content);
    }

    public function show(string $slug): string //Response
    {
        return $this->renderer->render('@blog/show', [
            'slug' => $slug
        ]);

//        $content = $this->renderer->render('@blog/show', [
//            'slug' => $request->getAttribute('slug')
//        ]);
//        return new \GuzzleHttp\Psr7\Response(200, [], $content);
    }
}
