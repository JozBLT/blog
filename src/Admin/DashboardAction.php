<?php

namespace App\Admin;

use Framework\Renderer\RendererInterface;

class DashboardAction
{

    private RendererInterface $renderer;

    /** @var AdminWidgetInterface[] */
    private array $widgets;

    public function __construct(RendererInterface $renderer, array $widgets)
    {
        $this->renderer = $renderer;
        $this->widgets = $widgets;
    }

    public function __invoke(): ?string
    {
        $widgets = array_reduce($this->widgets, function (string $html, AdminWidgetInterface $widget) {
            return $html . $widget->render();
        }, '');

        return $this->renderer->render('@admin/dashboard', compact('widgets'));
    }
}
