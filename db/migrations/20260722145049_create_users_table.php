<?php

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users', ['id' => 'user_id']);
        $table->addColumn('email', 'string', ['limit' => 255])
              ->addColumn('password_hash', 'string', ['limit' => 255])
              ->addColumn('full_name', 'string', ['limit' => 255])
              ->addColumn('role', 'enum', ['values' => ['student', 'professor', 'admin']])

              ->addColumn('department', 'string', ['limit' => 150, 'null' => true])
              ->addColumn('title', 'string', ['limit' => 100, 'null' => true])

              ->addColumn('student_number', 'string', ['limit' => 50, 'null' => true])
              ->addColumn('program', 'string', ['limit' => 150, 'null' => true])
              ->addColumn('year_level', 'string', ['limit' => 50, 'null' => true])

              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at', 'timestamp', [
                  'default' => 'CURRENT_TIMESTAMP',
                  'update'  => 'CURRENT_TIMESTAMP',
              ])
              ->addIndex(['email'], ['unique' => true])
              ->addIndex(['student_number'], ['unique' => true])
              ->create();

    }
}