<?php

namespace App\Auth;

use Framework\Database\Repository;

class UserRepository extends Repository
{

    protected /*string */$repository = "users";

    public function __construct(\PDO $pdo, string $entity = User::class)
    {
        $this->entity = $entity;
        parent::__construct($pdo);
    }
}
