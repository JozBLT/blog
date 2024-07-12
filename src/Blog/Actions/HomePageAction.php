<?php

namespace App\Blog\Actions;

use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Session\FlashService;

class HomePageAction
{

    private RendererInterface $renderer;

    private FlashService $flashService;

    use RouterAwareAction;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function __invoke(): string
    {
        return $this->renderer->render('@blog/homepage');
    }
}