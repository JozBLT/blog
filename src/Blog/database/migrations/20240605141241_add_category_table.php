<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddCategoryTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('categories')
            ->addColumn('id', 'integer', ['identity' => true])
            ->addColumn('name', 'string')
            ->addColumn('slug', 'string')
            ->addIndex(['id'], ['unique' => true])
            ->create();
    }
}
