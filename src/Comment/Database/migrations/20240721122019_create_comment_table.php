<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCommentTable extends AbstractMigration
{

    public function change(): void
    {
        $this->table('comments')
            ->addColumn('post_id', 'integer', ['null' => false, 'signed' => false])
            ->addForeignKey('post_id', 'posts', 'id', [
                'delete' => 'CASCADE'
            ])
            ->addColumn('username', 'string')
            ->addColumn('comment', 'text', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_REGULAR])
            ->addColumn('created_at', 'datetime')
            ->addColumn('published', 'boolean', ['default' => false])
            ->create();
    }
}
