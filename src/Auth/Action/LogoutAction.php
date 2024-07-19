<?php

namespace App\Auth\Action;

use App\Auth\DatabaseAuth;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;

class LogoutAction
{

    private DatabaseAuth $auth;

    private FlashService $flashService;

    public function __construct(DatabaseAuth $auth, FlashService $flashService)
    {
        $this->auth = $auth;
        $this->flashService = $flashService;
    }

    public function __invoke(ServerRequestInterface $request): RedirectResponse
    {
        $this->auth->logout();
        $this->flashService->success('Vous êtes bien déconnecté');

        return new RedirectResponse('/');
    }
}
