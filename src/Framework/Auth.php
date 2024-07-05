<?php

namespace Framework;

use Framework\Auth\User;

interface Auth
{

    public function getUser(): ?User;
}
