<?php

namespace App\Auth;

use App\Auth\User as AppUser;
use Framework\Auth;
use Framework\Auth\User;
use Framework\Database\NoRecordException;
use Framework\Session\SessionInterface;

class DatabaseAuth implements Auth
{

    private UserRepository $userRepository;

    private SessionInterface $session;

    private ?AppUser $user = null;

    public function __construct(UserRepository $userRepository, SessionInterface $session)
    {
        $this->userRepository = $userRepository;
        $this->session = $session;
    }

    public function login(string $username, string $password): ?User
    {
        if (empty($username) || empty($password)) {
            return null;
        }

        try {
            /** @var AppUser $user */
            $user = $this->userRepository->findBy('username', $username);
        } catch (NoRecordException) {
            return null;
        }

        if ($user && password_verify($password, $user->password)) {
            $this->setUser($user);

            return $user;
        }

        return null;
    }

    public function logout(): void
    {
        $this->session->delete('auth.user');
    }

    public function getUser(): ?User
    {
        if ($this->user instanceof AppUser) {
            return $this->user;
        }
        $userId = $this->session->get('auth.user');

        if ($userId) {
            try {
                $this->user = $this->userRepository->find($userId);

                return $this->user;
            } catch (NoRecordException) {
                $this->session->delete('auth.user');

                return null;
            }
        }

        return null;
    }

    public function setUser(AppUser $user): void
    {
        $this->session->set('auth.user', $user->id);
        $this->user = $user;
    }
}
