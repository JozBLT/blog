<?php

namespace Framework;

use DI\ContainerBuilder;
use Exception;
use Framework\Middleware\CombinedMiddleware;
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

    /** List of modules */
    private array $modules = [];

    private string|array|null $definitions;

    private ?ContainerInterface $container = null;

    /** @var string[] */
    private array $middlewares = [];

    private int $index = 0;

    public function __construct($definitions = null)
    {
        $this->definitions = $definitions;
    }

    /** Add a module to the app */
    public function addModule(string $module): self
    {
        $this->modules[] = $module;

        return $this;
    }

    /**
     * Add a middleware
     *
     * @throws Exception
     */
    public function pipe(
        string|callable|MiddlewareInterface $routePrefix,
        string|callable|MiddlewareInterface|null $middleware = null
    ): self {
        if ($middleware === null) {
            $this->middlewares[] = $routePrefix;
        } else {
            $this->middlewares[] = new RoutePrefixedMiddleware($this->getContainer(), $routePrefix, $middleware);
        }

        return $this;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->index++;

        if ($this->index > 1) {
            throw new Exception();
        }
        $middleware = new CombinedMiddleware($this->getContainer(), $this->middlewares);

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
        if ($this->container === null) {
            $builder = new ContainerBuilder();
            $env = $_ENV['ENV'] ?? 'production';

            if ($env === 'production') {
                $builder->enableCompilation(__DIR__ . '/tmp');
                $builder->writeProxiesToFile(true, __DIR__ . '/tmp/proxies');
            }

            if ($this->definitions) {
                $builder->addDefinitions($this->definitions);
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

    public function getModules(): array
    {
        return $this->modules;
    }
}
