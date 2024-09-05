<?php

use App\Account\AccountModule;
use App\Admin\AdminModule;
use App\Auth\AuthModule;
use App\Auth\ForbiddenMiddleware;
use App\Blog\BlogModule;
use App\Comment\CommentModule;
use App\Contact\ContactModule;
use Dotenv\Dotenv;
use Framework\App;
use Framework\Auth\RoleMiddlewareFactory;
use Framework\Middleware\CsrfMiddleware;
use Framework\Middleware\DispatcherMiddleware;
use Framework\Middleware\MethodMiddleware;
use Framework\Middleware\NotFoundMiddleware;
use Framework\Middleware\RendererRequestMiddleware;
use Framework\Middleware\RouterMiddleware;
use Framework\Middleware\TrailingSlashMiddleware;
use Franzl\Middleware\Whoops\WhoopsMiddleware;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use function Http\Response\send;

\chdir(dirname(__DIR__));

require 'vendor/autoload.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

\date_default_timezone_set('Europe/Paris');

$app =  (new App('config/config.php'))
    ->addModule(AdminModule::class)
    ->addModule(ContactModule::class)
    ->addModule(BlogModule::class)
    ->addModule(AuthModule::class)
    ->addModule(AccountModule::class)
    ->addModule(CommentModule::class);

try {
    $container = $app->getContainer();
    $adminMiddleware = $container->get(RoleMiddlewareFactory::class);
    \assert($adminMiddleware instanceof RoleMiddlewareFactory);
    $app->pipe(WhoopsMiddleware::class)
        ->pipe(TrailingSlashMiddleware::class)
        ->pipe(ForbiddenMiddleware::class)
        ->pipe($container->get('admin.prefix'), $adminMiddleware->makeForRole('admin'))
        ->pipe(MethodMiddleware::class)
        ->pipe(RendererRequestMiddleware::class)
        ->pipe(CsrfMiddleware::class)
        ->pipe(RouterMiddleware::class)
        ->pipe(DispatcherMiddleware::class)
        ->pipe(NotFoundMiddleware::class);
} catch (NotFoundExceptionInterface|ContainerExceptionInterface|Exception $e) {
    echo 'Erreur : ' . $e->getMessage();
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}

if (php_sapi_name() !== "cli") {
    try {
        $response = $app->run(ServerRequest::fromGlobals());
        send($response);
    } catch (NotFoundExceptionInterface|ContainerExceptionInterface|Exception $e) {
        echo 'Erreur : ' . $e->getMessage();
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    }
}
