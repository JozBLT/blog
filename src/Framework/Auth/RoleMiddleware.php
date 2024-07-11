<?php

namespace Framework\Auth;

use Framework\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoleMiddleware implements MiddlewareInterface
{

    /**
     * @var Auth
     */
    private Auth $auth;

    /**
     * @var string
     */
    private string $role;

    public function __construct(Auth $auth, string $role)
    {
        $this->auth = $auth;
        $this->role = $role;
    }

    /**
     * @throws ForbiddenException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->auth->getUser();

        if ($user === null || !in_array($this->role, $user->getRoles())) {
            throw new ForbiddenException();
        }

        return $handler->handle($request);
    }
}
