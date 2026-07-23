<?php

use Phinx\Migration\AbstractMigration;
 
final class CreateGroupMemberTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('group_members', ['id' => 'group_member_id']);
 
        $table->addColumn('group_id', 'integer', ['signed' => false])
              ->addColumn('student_id', 'integer', ['signed' => false])
              ->addColumn('is_leader', 'boolean', ['default' => false])
              ->addColumn('added_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addForeignKey('group_id', 'thesis_groups', 'group_id', [
                  'delete' => 'CASCADE',
                  'update' => 'NO_ACTION',
              ])
              ->addForeignKey('student_id', 'users', 'user_id', [
                  'delete' => 'RESTRICT',
                  'update' => 'NO_ACTION',
              ])
              ->addIndex(['student_id'], ['unique' => true])
              ->create();
    }
}
 
