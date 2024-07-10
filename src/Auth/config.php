<?php

use App\Auth\AuthTwigExtension;
use App\Auth\DatabaseAuth;
use App\Auth\ForbiddenMiddleware;
use App\Auth\User;
use App\Auth\UserRepository;
use Framework\Auth;
use function DI\add;
use function DI\autowire;
use function DI\factory;
use function DI\get;

return [
    'auth.login' => '/login',
    'auth.entity' => User::class,
    'twig.extensions' => add([
        get(AuthTwigExtension::class)
    ]),
    Auth\User::class => factory(function (Auth $auth) {
        return $auth->getUser();
    })->parameter('auth', get(Auth::class)),
    Auth::class => get(DatabaseAuth::class),
    UserRepository::class => autowire()->constructorParameter('entity', get('auth.entity')),
    ForbiddenMiddleware::class => autowire()->constructorParameter('loginPath', get('auth.login'))
];
