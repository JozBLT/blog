<?php

namespace App\Auth\Action;

use App\Auth\Mailer\PasswordResetMailer;
use App\Auth\UserRepository;
use Framework\Database\NoRecordException;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class PasswordReminderAction
{

    private RendererInterface $renderer;

    private UserRepository $userRepository;

    private PasswordResetMailer $mailer;

    private FlashService $flashService;

    public function __construct(
        RendererInterface $renderer,
        UserRepository $userRepository,
        PasswordResetMailer $mailer,
        FlashService $flashService
    ) {
        $this->renderer = $renderer;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->flashService = $flashService;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'GET') {
            return $this->renderer->render('@auth/reminder');
        }

        $params = $request->getParsedBody();
        $validator = (new Validator($params))
            ->notEmpty('email')
            ->email('email');

        if ($validator->isValid()) {
            try {
                $user = $this->userRepository->findBy('email', $params['email']);
                $token = $this->userRepository->resetPassword($user->id);
                $this->mailer->send($user->email, [
                    'id' => $user->id,
                    'token' => $token
                ]);
                $this->flashService->success('La procédure de reset vous a été envoyée par email');

                return new RedirectResponse($request->getUri()->getPath());
            } catch (NoRecordException $e) {
                $errors = ['email' => 'Aucun utilisateur ne corrsepond à cet email'];
            }
        } else {
            $errors = $validator->getErrors();
        }

        return $this->renderer->render('@auth/reminder', compact('errors'));
    }
}
