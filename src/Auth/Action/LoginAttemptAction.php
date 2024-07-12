<?php

namespace App\Auth\Action;

use App\Auth\DatabaseAuth;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginAttemptAction
{

    /**
     * @var RendererInterface
     */
    private $renderer;
    /**
     * @var DatabaseAuth
     */
    private $auth;
    private Router $router;
    /**
     * @var SessionInterface
     */
    private $session;

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

    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        $user = $this->auth->login($params['username'], $params['password']);

        if ($user) {
            $path = $this->session->get('auth.redirect');

            if (!$path && $user->getRole() === 'admin') {
                $path = $this->router->generateUri('admin');
            } else {
                $path = $this->router->generateUri('homepage');
            }

            $this->session->delete('auth.redirect');

            return new RedirectResponse($path);
        } else {
            (new FlashService($this->session))->error('Identifiant ou mot de passe incorrect');

            return $this->redirect('auth.login');
        }
    }
}
