<?php

namespace App\Auth\Action;

use App\Auth\DatabaseAuth;
use Exception;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginAttemptAction
{

    private RendererInterface $renderer;

    private DatabaseAuth $auth;

    private Router $router;

    private SessionInterface $session;

    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        DatabaseAuth $auth,
        Router $router,
        SessionInterface $session
    ) {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->router = $router;
        $this->session = $session;
    }

    /** @throws Exception */
    public function __invoke(ServerRequestInterface $request): ResponseInterface|RedirectResponse
    {
        $params = $request->getParsedBody();
        $user = $this->auth->login($params['username'], $params['password']);

        if ($user) {

            if ($user->getRole() === 'admin') {
                $path = $this->router->generateUri('admin');
            } else {
                $path = $this->session->get('auth.redirect') ?: $this->router->generateUri('homepage');
            }
            $this->session->delete('auth.redirect');

            return new RedirectResponse($path);

        } else {
            (new FlashService($this->session))->error('Identifiant ou mot de passe incorrect');

            return $this->redirect('auth.login');
        }
    }
}
