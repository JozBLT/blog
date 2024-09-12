<?php

namespace App\Contact;

use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class ContactAction
{

    private string $to;

    private RendererInterface $renderer;

    private FlashService $flashService;

    private Mailer $mailer;

    private SessionInterface $session;

    public function __construct(
        string $to,
        RendererInterface $renderer,
        FlashService $flashService,
        Mailer $mailer,
        SessionInterface $session
    ) {
        $this->renderer = $renderer;
        $this->to = $to;
        $this->flashService = $flashService;
        $this->mailer = $mailer;
        $this->session = $session;
    }

    /** @throws TransportExceptionInterface */
    public function __invoke(ServerRequestInterface $request): string|RedirectResponse
    {
        $referer = $request->getServerParams()['HTTP_REFERER'] ?? '/';

        if ($request->getMethod() === 'GET') {
            $errors = $this->session->get('errors', []);
            $old = $this->session->get('old', []);
            $this->session->delete('errors');
            $this->session->delete('old');

            return $this->renderer->render('@contact/contact', compact('errors', 'old'));
        }

        $params = $request->getParsedBody();
        $validator = (new Validator($params))
            ->required('name', 'email', 'content')
            ->length('name', 5)
            ->email('email')
            ->length('content', 15);

        if ($validator->isValid()) {
            $email = (new Email())
                ->from($this->to)
                ->to($this->to)
                ->replyTo($params['email'])
                ->subject('Formulaire de contact')
                ->text($this->renderer->render('@contact/email/contact.text', $params))
                ->html($this->renderer->render('@contact/email/contact.html', $params));
            $this->mailer->send($email);
            $this->flashService->success('Merci pour votre email');
        } else {
            $this->session->set('errors', $validator->getErrors());
            $this->session->set('old', $params);
            $this->flashService->error('Merci de corriger vos erreur');
        }

        return new RedirectResponse($referer);
    }
}
