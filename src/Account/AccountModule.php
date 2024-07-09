<?php

namespace App\Account;

use App\Account\Action\SignUpAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;

class AccountModule extends Module
{

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('account', __DIR__ . '/views');
        $router->get('/inscription', SignUpAction::class, 'account.signup');
        $router->post('/inscription', SignUpAction::class);
        $router->get('/mon-profile', SignUpAction::class, 'account.profile');
    }
}
