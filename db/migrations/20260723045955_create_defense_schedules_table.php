<?php

use Phinx\Migration\AbstractMigration;
 
final class CreateDefenseSchedulesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('defense_schedules', ['id' => 'schedule_id']);
 
        $table->addColumn('group_id', 'integer', ['signed' => false])
              ->addColumn('request_id', 'integer', ['signed' => false])
              ->addColumn('defense_date', 'date')
              ->addColumn('start_time', 'time')
              ->addColumn('end_time', 'time')
              ->addColumn('venue', 'string', ['limit' => 150, 'null' => true])
              ->addColumn('status', 'enum', [
                  'values'  => ['scheduled', 'completed', 'cancelled', 'rescheduled'],
                  'default' => 'scheduled',
              ])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addForeignKey('group_id', 'thesis_groups', 'group_id', [
                  'delete' => 'RESTRICT',
                  'update' => 'NO_ACTION',
              ])
              ->addForeignKey('request_id', 'schedule_requests', 'request_id', [
                  'delete' => 'RESTRICT',
                  'update' => 'NO_ACTION',
              ])
              ->addIndex(['request_id'], ['unique' => true])
              ->create();
    }
}
 
