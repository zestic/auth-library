<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class CreateUserIdentifiersTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('user_identifiers');
        $table
            ->addColumn('user_id', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('provider', 'string', ['limit' => 64])
            ->addColumn('identifier_id', 'string', ['limit' => 128])
            ->addColumn('raw_data', 'text', ['null' => true])
            ->addIndex(['provider', 'identifier_id'])
            ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
            ->create();
    }
}
