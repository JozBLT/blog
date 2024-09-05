<?php

namespace Framework;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class MailerFactory
{

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Mailer
    {
        if ($container->get('env') === 'production') {
            $host = $_ENV['SMTP_HOST'];
            $port = $_ENV['SMTP_PORT'];
            $username = $_ENV['SMTP_USER'];
            $password = $_ENV['SMTP_PASS'];

            $transport = new EsmtpTransport($host, $port, true);
            $transport->setUsername($username);
            $transport->setPassword($password);
        } else {
            $transport = new EsmtpTransport('localhost', 1025);
        }

        return new Mailer($transport);
    }
}
