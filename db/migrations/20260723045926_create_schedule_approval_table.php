<?php

use Phinx\Migration\AbstractMigration;

final class CreateScheduleApprovalsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('schedule_approvals', ['id' => 'approval_id']);

        $table->addColumn('request_id', 'integer', ['signed' => false])
              ->addColumn('professor_id', 'integer', ['signed' => false])
              ->addColumn('role', 'enum', ['values' => ['adviser', 'chair', 'critic']])
              ->addColumn('status', 'enum', [
                  'values'  => ['pending', 'approved', 'rejected'],
                  'default' => 'pending',
              ])
              ->addColumn('remarks', 'string', ['limit' => 500, 'null' => true])
              ->addColumn('responded_at', 'timestamp', ['null' => true])
              ->addForeignKey('request_id', 'schedule_requests', 'request_id', [
                  'delete' => 'CASCADE',
                  'update' => 'NO_ACTION',
              ])
              ->addForeignKey('professor_id', 'users', 'user_id', [
                  'delete' => 'RESTRICT',
                  'update' => 'NO_ACTION',
              ])
              ->addIndex(['request_id', 'professor_id'], ['unique' => true])
              ->create();
    }
}