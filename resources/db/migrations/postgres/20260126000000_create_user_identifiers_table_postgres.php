<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

use Phinx\Config\FeatureFlags;

class CreateUserIdentifiersTablePostgres extends AbstractMigration
{
    public function change(): void
    {
        $columnType = FeatureFlags::$addTimestampsUseDateTime ? 'datetime' : 'timestamp';
        $table = $this->table('user_identifiers');
        $table
            ->addColumn('user_id', 'uuid')
            ->addColumn('provider', 'string', ['limit' => 64])
            ->addColumn('identifier_id', 'string', ['limit' => 128])
            ->addColumn('raw_data', 'text', ['null' => true])
            ->addColumn('deleted_at', $columnType, ['null' => true])
            ->addIndex(['provider', 'identifier_id'])
            ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
            ->create();
    }
}
