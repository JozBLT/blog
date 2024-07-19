<?php

namespace App\Auth;

use DateTime;
use Exception;

class User implements \Framework\Auth\User
{

    public int $id;

    public string $username;

    public string $email;

    public string $password;

    public ?string $passwordReset;

    public ?DateTime $passwordResetAt;

    public function getUsername(): string
    {
        return $this->username;
    }

    /** @return string[] */
    public function getRoles(): array
    {
        return [];
    }

    public function getPasswordReset(): ?string
    {
        return $this->passwordReset;
    }

    /**
     * @param mixed $passwordReset
     */
    public function setPasswordReset(?string $passwordReset): void
    {
        $this->passwordReset = $passwordReset;
    }

    /** @throws Exception */
    public function setPasswordResetAt(?string $date): void
    {
        if (is_string($date)) {
            $this->passwordResetAt = new DateTime($date);
        } else {
            $this->passwordResetAt = $date;
        }
    }

    public function getPasswordResetAt(): ?DateTime
    {
        return $this->passwordResetAt;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
