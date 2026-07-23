<?php

use Phinx\Migration\AbstractMigration;

final class CreateScheduleRequestTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('schedule_requests', ['id' => 'request_id']);

        $table->addColumn('group_id', 'integer', ['signed' => false])
              ->addColumn('requested_by', 'integer', ['signed' => false])
              ->addColumn('defense_date', 'date')
              ->addColumn('start_time', 'time')
              ->addColumn('end_time', 'time')
              ->addColumn('status', 'enum', [
                  'values'  => ['pending', 'approved', 'rejected', 'cancelled'],
                  'default' => 'pending',
              ])
              ->addColumn('requested_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('finalized_at', 'timestamp', ['null' => true])
              ->addForeignKey('group_id', 'thesis_groups', 'group_id', [
                  'delete' => 'CASCADE',
                  'update' => 'NO_ACTION',
              ])
              ->addForeignKey('requested_by', 'users', 'user_id', [
                  'delete' => 'RESTRICT',
                  'update' => 'NO_ACTION',
              ])
              ->create();
    }
}