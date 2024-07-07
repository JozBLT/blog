<?php

namespace Framework;

use DI\ContainerBuilder;
use Exception;
use Framework\Middleware\RoutePrefixedMiddleware;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class App implements RequestHandlerInterface
{

    /**
     * List of modules
     * @var array
     */
    private $modules = [];
    /**
     * @var string|array|null
     */
    private $definition;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string[]
     */
    private $middlewares = [];

    /**
     * @var int
     */
    private $index = 0;

    public function __construct($definition = null)
    {
        $this->definition = $definition;
    }

    /** Add a module to the app */
    public function addModule(string $module): self
    {
        $this->modules[] = $module;

        return $this;
    }

    /** Add a middleware */
    public function pipe(
        string|callable|MiddlewareInterface $routePrefix,
        string|callable|MiddlewareInterface|null $middleware = null
    ): self
    {
        if ($middleware === null) {
            $this->middlewares[] = $routePrefix;
        } else {
            $this->middlewares[] = new RoutePrefixedMiddleware($this->getContainer(), $routePrefix, $middleware);
        }

        return $this;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();

        if (is_null($middleware)) {
            throw new Exception('Aucun middleware n\'a intercepté cette requête');
        } elseif (is_callable($middleware)) {
            return call_user_func_array($middleware, [$request, [$this, 'handle']]);
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }

        return $middleware->process($request, $this);
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }

        return $this->handle($request);
    }

    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new ContainerBuilder();

            if ($this->definition) {
                $builder->addDefinitions($this->definition);
            }

            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }

            $this->container = $builder->build();
        }

        return $this->container;
    }

    /**
     * @return object
     */
    private function getMiddleware()
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            if (is_string($this->middlewares[$this->index])) {
                $middleware = $this->container->get($this->middlewares[$this->index]);
            } else {
                $middleware = $this->middlewares[$this->index];
            }
            $this->index++;
            return $middleware;
        }

        return null;
    }

    public function getModules(): array
    {
        return $this->modules;
    }
}
