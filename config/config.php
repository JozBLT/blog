<?php

use Framework\MailerFactory;

use Framework\Middleware\CsrfMiddleware;
use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router;
use Framework\Router\RouterFactory;
use Framework\Router\RouterTwigExtension;
use Framework\Session\PHPSession;
use Framework\Session\SessionInterface;
use Framework\Twig\CsrfExtension;
use Framework\Twig\FlashExtension;
use Framework\Twig\FormExtension;
use Framework\Twig\PagerFantaExtension;
use Framework\Twig\TextExtension;
use Framework\Twig\TimeExtension;
use Psr\Container\ContainerInterface;
use Symfony\Component\Mailer\Mailer;

use function DI\get;
use function DI\autowire;
use function DI\factory;
use function DI\env;

return [
    'env' => env('ENV', 'production'),
    'database.host' => 'localhost',
    'database.username' => 'root',
    'database.password' => '',
    'database.name' => 'blog',
    'views.path' => dirname(__DIR__) . '/views',
    'twig.extensions' => [
      get(RouterTwigExtension::class),
      get(PagerFantaExtension::class),
      get(TextExtension::class),
      get(TimeExtension::class),
      get(FlashExtension::class),
      get(FormExtension::class),
      get(CsrfExtension::class)
    ],
    SessionInterface::class => autowire(PHPSession::class),
    CsrfMiddleware::class => function (ContainerInterface $c) {
        $session = $c->get(SessionInterface::class);
        return new CsrfMiddleware($session);
    },
    Router::class => factory(RouterFactory::class),
    RendererInterface::class => factory(TwigRendererFactory::class),
    PDO::class => function (ContainerInterface $c) {
        return  new PDO(
            'mysql:host=' . $c->get('database.host') . ';dbname=' . $c->get('database.name'),
            $c->get('database.username'),
            $c->get('database.password'),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    },
    //Mailer
    'mail.to' => 'admin@admin.fr', // créer une boite mail pour la réception
    'mail.from' => 'no-reply@blogAdmin.fr',
    Mailer::class => factory(MailerFactory::class)
];
