<?php

use App\Blog\BlogModule;
//use App\Blog\DemoExtension;
use function \DI\autowire;
use function \DI\get;
use function \DI\add;

return [
    'blog.prefix' => '/blog',
//    'twig.extensions' => add([
//        get(DemoExtension::class)
//    ]),
    BlogModule::class => autowire()->constructorParameter('prefix', get('blog.prefix'))
];
