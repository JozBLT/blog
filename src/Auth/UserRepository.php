<?php

namespace App\Auth;

use Framework\Database\Repository;

class UserRepository extends Repository
{

    protected /*string */$repository = "users";

    protected /*?string */$entity = User::class;
}
