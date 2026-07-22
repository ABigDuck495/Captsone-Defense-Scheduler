<?php

use Phinx\Migration\AbstractMigration;

final class CreateStudentsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('students', [
            'id'          => false,
            'primary_key' => 'student_id',
        ]);

        $table->addColumn('student_id', 'integer', ['signed' => false])
              ->addColumn('student_number', 'string', ['limit' => 50, 'null' => true])
              ->addColumn('program', 'string', ['limit' => 150, 'null' => true])
              ->addColumn('year_level', 'string', ['limit' => 50, 'null' => true])
              ->addForeignKey('student_id', 'users', 'user_id', [
                  'delete' => 'CASCADE',
                  'update' => 'NO_ACTION',
              ])
              ->addIndex(['student_number'], ['unique' => true])
              ->create();
    }
}