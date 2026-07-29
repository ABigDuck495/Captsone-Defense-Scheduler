<?php

use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{
    public function run(): void
    {
        $seed1 = [
            [
                'email'         => 'mikkimartin.agapito@clsu.edu.ph',
                'password_hash' => password_hash('Pass123', PASSWORD_BCRYPT),
                'full_name'     => 'Mikki Martin Agapito',
                'role'          => 'student',
                'department'    => 'Cen',
                'title'         => null,
                'student_number'=> '23-2634',
                'program'       => 'BSIT',
                'year_level'    => 'Graduating',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];
        $seed2 = [
            [
                'email'         => 'janusgabriel.ramos@clsu.edu.ph',
                'password_hash' => password_hash('Pass123', PASSWORD_BCRYPT),
                'full_name'     => 'Janus Gabriel Ramos',
                'role'          => 'student',
                'department'    => 'Cen',
                'title'         => null,
                'student_number'=> '23-2625',
                'program'       => 'BSIT',
                'year_level'    => null,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];
        $seed3 = [
            [
                'email'         => 'professor.one@clsu.edu.ph',
                'password_hash' => password_hash('Pass123', PASSWORD_BCRYPT),
                'full_name'     => 'Professor One',
                'role'          => 'student',
                'department'    => 'Cen',
                'title'         => 'Instructor',
                'student_number'=> null,
                'program'       => 'BSIT',
                'year_level'    => null,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];
        $seed4 = [
            [
                'email'         => 'professor.two@clsu.edu.ph',
                'password_hash' => password_hash('Pass123', PASSWORD_BCRYPT),
                'full_name'     => 'Professor Two',
                'role'          => 'student',
                'department'    => 'Cen',
                'title'         => 'Professor',
                'student_number'=> null,
                'program'       => 'BSIT',
                'year_level'    => null,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];
        $seed5 = [
            [
                'email'         => 'professor.three@clsu.edu.ph',
                'password_hash' => password_hash('Pass123', PASSWORD_BCRYPT),
                'full_name'     => 'Professor Three',
                'role'          => 'student',
                'department'    => 'Cen',
                'title'         => 'Department Head',
                'student_number'=> null,
                'program'       => 'BSIT',
                'year_level'    => null,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        $this->table('users')->insert($seed1)->saveData();
        $this->table('users')->insert($seed2)->saveData();
        $this->table('users')->insert($seed3)->saveData();
        $this->table('users')->insert($seed4)->saveData();
        $this->table('users')->insert($seed5)->saveData();
    }
}
