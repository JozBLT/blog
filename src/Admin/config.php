<?php

use App\Admin\AdminModule;
use App\Admin\AdminTwigExtension;
use App\Admin\DashboardAction;
use function DI\autowire;
use function DI\get;

return [
    'admin.prefix' => '/admin',
    'admin.widgets' => [],
    AdminTwigExtension::class => autowire()->constructor(get('admin.widgets')),
    AdminModule::class => autowire()->constructorParameter('prefix', get('admin.prefix')),
    DashboardAction::class => autowire()->constructorParameter('widgets', get('admin.widgets'))
];
