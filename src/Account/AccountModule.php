<?php

namespace App\Account;

use App\Account\Action\AccountAction;
use App\Account\Action\AccountEditAction;
use App\Account\Action\SignUpAction;
use Framework\Auth\LoggedInMiddleware;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;

class AccountModule extends Module
{

    const MIGRATIONS  = __DIR__ . '/Database/migrations';

    const DEFINITIONS = __DIR__ . '/definitions.php';

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('account', __DIR__ . '/views');
        $router->get('/inscription', SignUpAction::class, 'account.signup');
        $router->post('/inscription', SignUpAction::class);
        $router->get('/mon-profile', [LoggedInMiddleware::class, AccountAction::class], 'account');
        $router->post('/mon-profile', [LoggedInMiddleware::class, AccountEditAction::class]);
    }
}
