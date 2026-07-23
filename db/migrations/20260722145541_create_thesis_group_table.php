<?php

use Phinx\Migration\AbstractMigration;
 
final class CreateThesisGroupTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('thesis_groups', ['id' => 'group_id']);
 
        $table->addColumn('thesis_title', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('status', 'enum', [
                  'values'  => ['forming', 'finalized', 'panel_assigned', 'defense_scheduled', 'completed', 'disbanded'],
                  'default' => 'forming',
              ])
              ->addColumn('created_by', 'integer', ['signed' => false])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('finalized_at', 'timestamp', ['null' => true])
              ->addForeignKey('created_by', 'users', 'user_id', [
                  'delete' => 'RESTRICT',
                  'update' => 'NO_ACTION',
              ])
              ->create();
    }
}
 
