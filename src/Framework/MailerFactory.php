<?php

namespace Framework;

use Psr\Container\ContainerInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\SendmailTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class MailerFactory
{

    public function __invoke(ContainerInterface $container): Mailer
    {
        if ($container->get('env') === 'production') {
            $transport = new SendmailTransport();
        } else {
            $transport = new EsmtpTransport('localhost', 1025);
        }

        return new Mailer($transport);
    }
}
