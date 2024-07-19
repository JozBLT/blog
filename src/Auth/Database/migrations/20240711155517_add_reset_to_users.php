<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddResetToUsers extends AbstractMigration
{
    public function change(): void
    {
        $this->table('users')
            ->addColumn('password_reset', 'string', ['default' => null])
            ->addColumn('password_reset_at', 'datetime', ['default' => null])
            ->update();
    }
}
