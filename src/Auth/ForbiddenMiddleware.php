<?php

namespace App\Auth;

use Framework\Auth\ForbiddenException;
use Framework\Auth\User;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TypeError;

class ForbiddenMiddleware implements MiddlewareInterface
{

    private string $loginPath;

    private SessionInterface $session;

    public function __construct(string $loginPath, SessionInterface $session)
    {
        $this->loginPath = $loginPath;
        $this->session = $session;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ForbiddenException) {
            return $this->redirectLogin($request);
        } catch (TypeError $error) {
            if (str_contains($error->getMessage(), User::class)) {
                return $this->redirectLogin($request);
            }
            throw $error;
        }
    }

    public function redirectLogin(ServerRequestInterface $request): ResponseInterface
    {
        $this->session->set('auth.redirect', $request->getUri()->getPath());
        (new FlashService($this->session))->error('Vous devez être administrateur pour accéder à cette page');

        return new RedirectResponse($this->loginPath);
    }
}
