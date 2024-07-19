<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddNameToUsers extends AbstractMigration
{
    public function change(): void
    {
        $this->table('users')
            ->addColumn('firstname', 'string')
            ->addColumn('lastname', 'string')
            ->update();
    }
}
