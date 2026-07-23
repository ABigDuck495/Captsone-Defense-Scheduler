<?php

use Phinx\Migration\AbstractMigration;
 
final class CreateProfessorAvailabilityTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('professor_availability', ['id' => 'availability_id']);
 
        $table->addColumn('professor_id', 'integer', ['signed' => false])
              ->addColumn('available_date', 'date')
              ->addColumn('start_time', 'time')
              ->addColumn('end_time', 'time') // typically start_time + 1 hour
              ->addColumn('status', 'enum', [
                  'values'  => ['available', 'booked', 'blocked'],
                  'default' => 'available',
              ])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addForeignKey('professor_id', 'users', 'user_id', [
                  'delete' => 'CASCADE',
                  'update' => 'NO_ACTION',
              ])
              ->addIndex(['professor_id', 'available_date', 'start_time'], ['unique' => true])
              ->addIndex(['professor_id', 'available_date', 'status'])
              ->create();
    }
}
 
