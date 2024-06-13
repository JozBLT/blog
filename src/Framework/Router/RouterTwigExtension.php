<?php

namespace Framework\Router;

use Exception;
use Framework\Router;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class RouterTwigExtension extends AbstractExtension
{

    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /** @return TwigFunction[] */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('path', [$this, 'pathFor']),
            new TwigFunction('is_subpath', [$this, 'isSubPath'])
        ];
    }

    /** @throws Exception */
    public function pathFor(string $path, array $params = []): string
    {
        return $this->router->generateUri($path, $params);
    }

    /** @throws Exception */
    public function isSubPath(string $path): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $expectedUri = $this->router->generateUri($path);
        return str_contains($uri, $expectedUri);
    }
}
