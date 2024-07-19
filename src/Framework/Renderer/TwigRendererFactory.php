<?php

namespace Framework\Renderer;

use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class TwigRendererFactory
{

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        $debug = $container->get('env') !== 'production';
        $viewPath = $container->get('views.path');
        $loader = new FilesystemLoader($viewPath);
        $twig = new Environment($loader, [
            'debug' => $debug,
            'cache' => $debug ? false : 'tmp/views',
            'auto_reload' => $debug
        ]);
        $twig->addExtension(new DebugExtension());

        if ($container->has('twig.extensions')) {

            foreach ($container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }

        return new TwigRenderer($twig);
    }
}
