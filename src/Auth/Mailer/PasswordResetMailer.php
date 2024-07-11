<?php

namespace App\Auth\Mailer;

use Framework\Renderer\RendererInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class PasswordResetMailer
{

    private Mailer $mailer;

    private RendererInterface $renderer;

    private string $from;

    public function __construct(Mailer $mailer, RendererInterface $renderer, string $from)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
        $this->from = $from;
    }

    public function send(string $to, array $params)
    {
        $email = (new Email())
            ->subject('Réinitialisation du mot de passe')
            ->from($this->from)
            ->to($to)
            ->text($this->renderer->render('@auth/email/reset.text', $params))
            ->html($this->renderer->render('@auth/email/reset.html', $params));
        $this->mailer->send($email);
    }
}
