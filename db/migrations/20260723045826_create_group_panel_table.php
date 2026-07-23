<?php

use Phinx\Migration\AbstractMigration;

final class CreateGroupPanelTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('group_panel', ['id' => 'panel_id']);

        $table->addColumn('group_id', 'integer', ['signed' => false])
              ->addColumn('professor_id', 'integer', ['signed' => false])
              ->addColumn('role', 'enum', ['values' => ['adviser', 'chair', 'critic']])
              ->addColumn('added_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addForeignKey('group_id', 'thesis_groups', 'group_id', [
                  'delete' => 'CASCADE',
                  'update' => 'NO_ACTION',
              ])
              ->addForeignKey('professor_id', 'users', 'user_id', [
                  'delete' => 'RESTRICT',
                  'update' => 'NO_ACTION',
              ])
              ->addIndex(['group_id', 'professor_id', 'role'], ['unique' => true])
              ->create();
    }
}