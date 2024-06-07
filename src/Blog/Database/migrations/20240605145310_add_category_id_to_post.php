<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddCategoryIdToPost extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('posts');
        if (!$table->hasColumn('category_id')) {
            $table->addColumn('category_id', 'integer', ['null' => true, 'signed' => false])
                ->addForeignKey('category_id', 'categories', 'id', [
                    'delete' => 'SET NULL'
                ])
                ->update();
        }
    }
}
