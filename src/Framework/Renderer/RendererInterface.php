<?php

namespace Framework\Renderer;

interface RendererInterface
{

    /** Add new path to change views */
    public function addPath(string $namespace, ?string $path = null): void;

    /**
     * Render a view
     * Path can be specified with namespaces added with addPath()
     * $this->render('@blog/views');
     * $this->render('view');
     */
    public function render(string $view, array $params = []): string;

    /** Add global variables on every views */
    public function addGlobal(string $key, mixed $value): void;
}
