<?php

namespace Framework\Auth;

interface User
{

    public function getUsername(): string;

    /**
     * @return string[]
     */
    public function getRoles(): array;
}
