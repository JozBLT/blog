<?php

use Framework\Router;
use Framework\Router\RouterTwigExtension;
use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;

return [
    'views.path' => dirname(__DIR__) . '/views',
    'twig.extensions' => [
      \DI\get(RouterTwigExtension::class)
    ],
    Router::class => \DI\autowire(),
    RendererInterface::class => \DI\factory(TwigRendererFactory::class)
];
