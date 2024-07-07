<?php

namespace App\Auth;

class User implements \Framework\Auth\User
{

    public /*int */$id;

    public /*string */$username;

    public /*string */$email;

    public /*string */$password;

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return [];
    }
}
