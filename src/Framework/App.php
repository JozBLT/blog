<?php

namespace Framework;

use DI\ContainerBuilder;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class App implements RequestHandlerInterface
{

    /** List of modules */
    private array $modules = [];
    private string $definition;
    private ContainerInterface $container;

    /**
     * @var string[]
     */
    private array $middlewares;
    private int $index = 0;

    public function __construct(string $definition)
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
    public function pipe(string $middleware): self
    {
        $this->middlewares[] = $middleware;

        return $this;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();

        if (is_null($middleware)) {
            throw new Exception('No middleware intercepted this request');
        } /*elseif (is_callable($middleware)) {
            return call_user_func_array($middleware, [$request, [$this, 'handle']]);
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }*/
        return $middleware->process($request, $this);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }
        return $this->handle($request);
    }

    /** @throws Exception */
    public function getContainer(): ContainerInterface
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions($this->definition);

        foreach ($this->modules as $module) {
            if ($module::DEFINITIONS) {
                $builder->addDefinitions($module::DEFINITIONS);
            }
        }
        $this->container = $builder->build();

        return $this->container;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getMiddleware(): ?object
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            $middleware = $this->container->get($this->middlewares[$this->index]);
            $this->index++;
            return $middleware;
        }
        return null;
    }
}
