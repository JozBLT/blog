<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddRoleToUser extends AbstractMigration
{
    public function change(): void
    {
        $this->table('users')
            ->addColumn('role', 'string', ['default' => 'user'])
            ->update();
    }
}
