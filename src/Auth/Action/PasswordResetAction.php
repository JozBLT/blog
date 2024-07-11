<?php

namespace App\Auth\Action;

use App\Auth\User;
use App\Auth\UserRepository;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class PasswordResetAction
{

    private RendererInterface $renderer;

    private UserRepository $userRepository;

    private Router $router;

    private FlashService $flashService;

    public function __construct(
        RendererInterface $renderer,
        UserRepository $userRepository,
        Router $router,
        FlashService $flashService
    ) {
        $this->renderer = $renderer;
        $this->userRepository = $userRepository;
        $this->router = $router;
        $this->flashService = $flashService;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        /** @var User $user */
        $user = $this->userRepository->find($request->getAttribute('id'));

        if ($user->getPasswordReset() !== null &&
            $user->getPasswordReset() === $request->getAttribute('token') &&
            time() - $user->getPasswordResetAt()->getTimestamp() < 600
        ) {
            if ($request->getMethod() === 'GET') {
                return $this->renderer->render('@auth/reset');
            } else {
                $params = $request->getParsedBody();
                $validator = (new Validator($params))
                    ->length('password', 4)
                    ->confirm('password');

                if ($validator->isValid()) {
                    $this->userRepository->updatePassword($user->getId(), $params['password']);
                    $this->flashService->success('Votre mot de passe a bien été changé');

                    return new RedirectResponse($this->router->generateUri('auth.login'));
                } else {
                    $errors = $validator->getErrors();

                    return $this->renderer->render('@auth/reset', compact('errors'));
                }
            }
        } else {
            $this->flashService->error('Token invalid');

            return new RedirectResponse($this->router->generateUri('auth.reminder'));
        }
    }
}
