<?php

namespace App\Account\Action;

use App\Auth\UserRepository;
use Framework\Auth;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class AccountEditAction
{

    private RendererInterface $renderer;

    private Auth $auth;

    private FlashService $flashService;

    private UserRepository $userRepository;

    public function __construct(
        RendererInterface $renderer,
        Auth $auth,
        FlashService $flashService,
        UserRepository $userRepository
    ) {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->flashService = $flashService;
        $this->userRepository = $userRepository;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $user = $this->auth->getUser();
        $params = $request->getParsedBody();
        $validator = (new Validator($params))
            ->confirm('password')
            ->required('firstname', 'lastname');

        if ($validator->isValid()) {
            $userParams = [
                'firstname' => $params['firstname'],
                'lastname' => $params['lastname']
            ];

            if (!empty($params['password'])) {
                $userParams['password'] = password_hash($params['password'], PASSWORD_DEFAULT);
            }

            $this->userRepository->update($user->id, $userParams);
            $this->flashService->success('Votre compte a bien été mis à jour');

            return new RedirectResponse($request->getUri()->getPath());
        }
        $errors = $validator->getErrors();

        return $this->renderer->render('@account/account', compact('user', 'errors'));
    }
}
