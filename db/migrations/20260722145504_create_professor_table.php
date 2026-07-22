<?php

use Phinx\Migration\AbstractMigration;

final class CreateProfessorsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('professors', [
            'id'          => false,
            'primary_key' => 'professor_id',
        ]);

        $table->addColumn('professor_id', 'integer', ['signed' => false])
              ->addColumn('department', 'string', ['limit' => 150, 'null' => true])
              ->addColumn('title', 'string', ['limit' => 100, 'null' => true])
              ->addForeignKey('professor_id', 'users', 'user_id', [
                  'delete' => 'CASCADE',
                  'update' => 'NO_ACTION',
              ])
              ->create();
    }
}