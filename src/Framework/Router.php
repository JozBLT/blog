<?php

namespace Framework;

use Framework\Middleware\CallableMiddleware;
use Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Mezzio\Router\FastRouteRouter;
use Mezzio\Router\Route as MezzioRoute;

/** Register and match routes */
class Router
{
    private FastRouteRouter $router;

    public function __construct(?string $cache = null)
    {
        $this->router = new FastRouteRouter(null, null, [
            FastRouteRouter::CONFIG_CACHE_ENABLED => !is_null($cache),
            FastRouteRouter::CONFIG_CACHE_FILE => $cache
        ]);
    }

    /** @param string|string[] $callable */
    public function get(string $path, string|array $callable, ?string $name = null): void
    {
        $this->router->addRoute(new MezzioRoute($path, new CallableMiddleware($callable), ['GET'], $name));
    }

    /** @param string|string[] $callable */
    public function post(string $path, string|array $callable, ?string $name = null): void
    {
        $this->router->addRoute(new MezzioRoute($path, new CallableMiddleware($callable), ['POST'], $name));
    }

    /** @param string|string[] $callable */
    public function delete(string $path, string|array $callable, ?string $name = null): void
    {
        $this->router->addRoute(new MezzioRoute($path, new CallableMiddleware($callable), ['DELETE'], $name));
    }

    /** @param string|string[] $callable */
    public function any(string $path, string|array $callable, ?string $name = null): void
    {
        $this->router->addRoute(new MezzioRoute($path, new CallableMiddleware($callable), [
            'GET',
            'POST',
            'DELETE',
            'PUT'
        ], $name));
    }

    /** Generate CRUD routes */
    public function crud(string $prefixPath, $callable, string $prefixName): void
    {
        $this->get("$prefixPath", $callable, "$prefixName.index");
        $this->get("$prefixPath/new", $callable, "$prefixName.create");
        $this->post("$prefixPath/new", $callable);
        $this->get("$prefixPath/{id:\d+}", $callable, "$prefixName.edit");
        $this->post("$prefixPath/{id:\d+}", $callable);
        $this->delete("$prefixPath/{id:\d+}", $callable, "$prefixName.delete");
    }

    public function match(ServerRequestInterface $request): ?Route
    {
        $result = $this->router->match($request);

        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(),
                $result->getMatchedRoute()->getMiddleware()->getCallable(),
                $result->getMatchedParams()
            );
        }

        return null;
    }

    public function generateUri(string $name, array $params = [], array $queryParams = []): ?string
    {
        $uri = $this->router->generateUri($name, $params);

        if (!empty($queryParams)) {
            return $uri . '?' . http_build_query($queryParams);
        }

        return $uri;
    }
}
