<?php

namespace Framework\Router;

use Framework\Router;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class RouterFactory
{

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Router
    {
        $cache = null;

        if ($container->get('env') === 'production') {
            $cache = 'tmp/routes';
        }

        return new Router($cache);
    }
}
