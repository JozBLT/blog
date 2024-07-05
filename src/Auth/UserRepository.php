<?php

namespace App\Auth;

use Framework\Database\Repository;

class UserRepository extends Repository
{

    protected ?string $entity = User::class;

    protected string $repository = 'users';
}
