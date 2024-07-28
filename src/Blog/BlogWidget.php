<?php

namespace App\Blog;

use App\Admin\AdminWidgetInterface;
use App\Blog\Repository\CategoryRepository;
use App\Blog\Repository\PostRepository;
use App\Comment\Repository\CommentRepository;
use Framework\Renderer\RendererInterface;

class BlogWidget implements AdminWidgetInterface
{

    private RendererInterface $renderer;

    private PostRepository $postRepository;

    private CategoryRepository $categoryRepository;

    private CommentRepository $commentRepository;

    public function __construct(
        RendererInterface $renderer,
        PostRepository $postRepository,
        CategoryRepository $categoryRepository,
        CommentRepository $commentRepository
    ) {
        $this->renderer = $renderer;
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
        $this->commentRepository = $commentRepository;
    }

    public function render(): string
    {
        $postCount = $this->postRepository->count();
        $categoryCount = $this->categoryRepository->count();
        $commentCount = $this->commentRepository->count();

        return $this->renderer->render(
            '@blog/admin/widget',
            compact('postCount', 'categoryCount', 'commentCount')
        );
    }

    public function renderMenu(): string
    {
        return $this->renderer->render('@blog/admin/menu');
    }
}
