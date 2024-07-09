<?php

namespace App\Contact;

use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class ContactAction
{

    private string $to;

    private RendererInterface $renderer;

    private FlashService $flashService;

    private Mailer $mailer;

    public function __construct(
        string $to,
        RendererInterface $renderer,
        FlashService $flashService,
        Mailer $mailer
    ) {
        $this->renderer = $renderer;
        $this->to = $to;
        $this->flashService = $flashService;
        $this->mailer = $mailer;
    }

    public function __invoke(ServerRequestInterface $request): string|RedirectResponse
    {
        if ($request->getMethod() === 'GET') {
            return $this->renderer->render('@contact/contact');
        }
        $params = $request->getParsedBody();
        $validator = (new Validator($params))
            ->required('name', 'email', 'content')
            ->length('name', 5)
            ->email('email')
            ->length('content', 15);

        if ($validator->isValid()) {
            $this->flashService->success('Merci pour votre email');
            $email = (new Email())
                ->from($params['email'])
                ->to($this->to)
                ->subject('Formulaire de contact')
                ->text($this->renderer->render('@contact/email/contact.text', $params))
                ->html($this->renderer->render('@contact/email/contact.html', $params));
            $this->mailer->send($email);

            return new RedirectResponse((string)$request->getUri());
        } else {
            $this->flashService->error('Merci de corriger vos erreur');
            $errors = $validator->getErrors();

            return $this->renderer->render('@contact/contact', compact('errors'));
        }
    }
}
