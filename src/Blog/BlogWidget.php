<?php

namespace App\Blog;

use App\Admin\AdminWidgetInterface;
use App\Blog\Repository\CategoryRepository;
use App\Blog\Repository\PostRepository;
use Framework\Renderer\RendererInterface;

class BlogWidget implements AdminWidgetInterface
{

    private RendererInterface $renderer;

    private PostRepository $postRepository;

    private CategoryRepository $categoryRepository;

    public function __construct(
        RendererInterface $renderer,
        PostRepository $postRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->renderer = $renderer;
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function render(): string
    {
        $postCount = $this->postRepository->count();
        $categoryCount = $this->categoryRepository->count();

        return $this->renderer->render(
            '@blog/admin/widget',
            compact('postCount', 'categoryCount')
        );
    }

    public function renderMenu(): string
    {
        return $this->renderer->render('@blog/admin/menu');
    }
}
