<?php

namespace Framework;

use AltoRouter;
use Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Register and match routes
 */
class Router
{

    /**
     * @var AltoRouter
     */
    private AltoRouter $router;

    public function __construct()
    {
        $this->router = new AltoRouter();
    }

    /**
     * @param string $path
     * @param callable|string $callable
     * @param string|null $name
     * @return void
     */
    public function get(string $path, callable|string $callable, ?string $name = null): void
    {
        try {
            $this->router->map('GET', $path, $callable, $name);
        } catch (\Exception) {
        }
    }

    /**
     * @param string $path
     * @param callable|string $callable
     * @param string|null $name
     * @return void
     */
    public function post(string $path, callable|string $callable, ?string $name = null): void
    {
        try {
            $this->router->map('POST', $path, $callable, $name);
        } catch (\Exception) {
        }
    }

    /**
     * @param string $path
     * @param callable|string $callable
     * @param string|null $name
     * @return void
     */
    public function delete(string $path, callable|string $callable, ?string $name = null): void
    {
        try {
            $this->router->map('DELETE', $path, $callable, $name);
        } catch (\Exception) {
        }
    }

    /**
     * Generate CRUD routes
     *
     * @param string $prefixPath
     * @param $callable
     * @param string $prefixName
     * @return void
     */
    public function crud(string $prefixPath, $callable, string $prefixName): void
    {
        $this->get("$prefixPath", $callable, "$prefixName.index");
        $this->get("$prefixPath/new", $callable, "$prefixName.create");
        $this->post("$prefixPath/new", $callable);
        $this->get("$prefixPath/[i:id]", $callable, "$prefixName.edit");
        $this->post("$prefixPath/[i:id]", $callable);
        $this->delete("$prefixPath/[i:id]", $callable, "$prefixName.delete");
    }

    /**
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        $result = $this->router->match($request->getUri()->getPath());
        if ($result != null) {
            return new Route($result['name'] ?? '', $result['target'], $result['params']);
        }
        return null;
    }

    /**
     * @param string $name
     * @param array $params
     * @param array $queryParams
     * @return string|null
     * @throws \Exception
     */
    public function generateUri(string $name, array $params = [], array $queryParams = []): ?string
    {
        $uri = $this->router->generate($name, $params);
        if (!empty($queryParams)) {
            return $uri . '?' . http_build_query($queryParams);
        }
        return $uri;
    }
}
