<?php

namespace Framework\Renderer;

interface RendererInterface
{
    /**
     * Add new path to change views
     * @param string $namespace
     * @param string|null $path
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void;

    /**
     * Render a view
     * Path can be specified with namespaces added with addPath()
     * $this->render('@blog/views');
     * $this->render('view');
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string;

    /**
     * Add global variables on every views
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addGlobal(string $key, mixed $value): void;
}
