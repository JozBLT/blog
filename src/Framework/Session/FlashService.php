<?php

namespace Framework\Session;

class FlashService
{

    private SessionInterface $session;

    private string $sessionKey = 'flash';

    private array $messages = [];

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function success(string $message): void
    {
        $flash = $this->session->get($this->sessionKey, []);
        $flash['success'] = $message;
        $this->session->set($this->sessionKey, $flash);
    }

    public function error(string $message): void
    {
        $flash = $this->session->get($this->sessionKey, []);
        $flash['error'] = $message;
        $this->session->set($this->sessionKey, $flash);
    }

    public function get(string $type): ?string
    {
        if ($this->messages === []) {
            $this->messages = $this->session->get($this->sessionKey, []);
            $this->session->delete($this->sessionKey);
        }

        return $this->messages[$type] ?? null;
    }
}
