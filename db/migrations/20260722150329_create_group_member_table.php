<?php

use Phinx\Migration\AbstractMigration;

final class CreateGroupMembersTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('group_members', ['id' => 'group_member_id']);

        $table->addColumn('group_id', 'integer', ['signed' => false])
              ->addColumn('student_id', 'integer', ['signed' => false])
              ->addColumn('status', 'enum', [
                  'values'  => ['invited', 'accepted', 'declined', 'removed'],
                  'default' => 'invited',
              ])
              ->addColumn('is_leader', 'boolean', ['default' => false])
              ->addColumn('invited_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('responded_at', 'timestamp', ['null' => true])
              ->addForeignKey('group_id', 'thesis_groups', 'group_id', [
                  'delete' => 'CASCADE',
                  'update' => 'NO_ACTION',
              ])
              ->addForeignKey('student_id', 'students', 'student_id', [
                  'delete' => 'RESTRICT',
                  'update' => 'NO_ACTION',
              ])
              ->addIndex(['group_id', 'student_id'], ['unique' => true])
              ->create();
    }
}