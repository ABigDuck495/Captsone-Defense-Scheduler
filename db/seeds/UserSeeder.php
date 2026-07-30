<?php

use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{
    public function run(): void
    {
        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $users = $this->table('users');
        $users->truncate();

        $passwordHash = password_hash('Pass123', PASSWORD_BCRYPT);

        // 6 Students (IDs 1–6)
        $students = [
            [
                'user_id'        => 1,
                'email'          => 'mikkimartin.agapito@clsu2.edu.ph',
                'password_hash'  => $passwordHash,
                'full_name'      => 'Mikki Martin Agapito',
                'role'           => 'student',
                'department'     => 'Cen',
                'title'          => null,
                'student_number' => '23-2634',
                'program'        => 'BSIT',
                'year_level'     => 'Graduating',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'        => 2,
                'email'          => 'janusgabriel.ramos@clsu2.edu.ph',
                'password_hash'  => $passwordHash,
                'full_name'      => 'Janus Gabriel Ramos',
                'role'           => 'student',
                'department'     => 'Cen',
                'title'          => null,
                'student_number' => '23-2625',
                'program'        => 'BSIT',
                'year_level'     => 'Graduating',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'        => 3,
                'email'          => 'student3@clsu2.edu.ph',
                'password_hash'  => $passwordHash,
                'full_name'      => 'Student Three',
                'role'           => 'student',
                'department'     => 'Cen',
                'title'          => null,
                'student_number' => 'S003',
                'program'        => 'BSIT',
                'year_level'     => 'Graduating',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'        => 4,
                'email'          => 'student4@clsu2.edu.ph',
                'password_hash'  => $passwordHash,
                'full_name'      => 'Student Four',
                'role'           => 'student',
                'department'     => 'Cen',
                'title'          => null,
                'student_number' => 'S004',
                'program'        => 'BSIT',
                'year_level'     => 'Graduating',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'        => 5,
                'email'          => 'student5@clsu2.edu.ph',
                'password_hash'  => $passwordHash,
                'full_name'      => 'Student Five',
                'role'           => 'student',
                'department'     => 'Cen',
                'title'          => null,
                'student_number' => 'S005',
                'program'        => 'BSIT',
                'year_level'     => 'Graduating',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'        => 6,
                'email'          => 'student6@clsu2.edu.ph',
                'password_hash'  => $passwordHash,
                'full_name'      => 'Student Six',
                'role'           => 'student',
                'department'     => 'Cen',
                'title'          => null,
                'student_number' => 'S006',
                'program'        => 'BSIT',
                'year_level'     => 'Graduating',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
        ];

        // 5 Professors (IDs 7–11)
        $professors = [
            [
                'user_id'        => 7,
                'email'          => 'prof1@clsu2.edu.ph',
                'password_hash'  => $passwordHash,
                'full_name'      => 'Professor Alpha',
                'role'           => 'professor',
                'department'     => 'Cen',
                'title'          => 'Professor',
                'student_number' => null,
                'program'        => null,
                'year_level'     => null,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'        => 8,
                'email'          => 'prof2@clsu2.edu.ph',
                'password_hash'  => $passwordHash,
                'full_name'      => 'Professor Beta',
                'role'           => 'professor',
                'department'     => 'Cen',
                'title'          => 'Professor',
                'student_number' => null,
                'program'        => null,
                'year_level'     => null,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'        => 9,
                'email'          => 'prof3@clsu2.edu.ph',
                'password_hash'  => $passwordHash,
                'full_name'      => 'Professor Gamma',
                'role'           => 'professor',
                'department'     => 'Cen',
                'title'          => 'Professor',
                'student_number' => null,
                'program'        => null,
                'year_level'     => null,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'        => 10,
                'email'          => 'prof4@clsu2.edu.ph',
                'password_hash'  => $passwordHash,
                'full_name'      => 'Professor Delta',
                'role'           => 'professor',
                'department'     => 'Cen',
                'title'          => 'Professor',
                'student_number' => null,
                'program'        => null,
                'year_level'     => null,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'        => 11,
                'email'          => 'prof5@clsu2.edu.ph',
                'password_hash'  => $passwordHash,
                'full_name'      => 'Professor Epsilon',
                'role'           => 'professor',
                'department'     => 'Cen',
                'title'          => 'Professor',
                'student_number' => null,
                'program'        => null,
                'year_level'     => null,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
        ];

        $users->insert(array_merge($students, $professors))->save();

        // ---------------------------
        // 2. THESIS GROUPS
        // ---------------------------
        $groups = $this->table('thesis_groups');
        $groups->truncate();
        $groups->insert([
            [
                'group_id'     => 1,
                'thesis_title' => 'AI-Based Scheduling System',
                'status'       => 'panel_assigned',
                'created_by'   => 1, // Student One
                'created_at'   => date('Y-m-d H:i:s'),
                'finalized_at' => date('Y-m-d H:i:s'),
            ],
            [
                'group_id'     => 2,
                'thesis_title' => 'Blockchain for Academic Records',
                'status'       => 'panel_assigned',
                'created_by'   => 4, // Student Four
                'created_at'   => date('Y-m-d H:i:s'),
                'finalized_at' => date('Y-m-d H:i:s'),
            ],
        ])->save();

        // ---------------------------
        // 3. GROUP MEMBERS
        // ---------------------------
        $members = $this->table('group_members');
        $members->truncate();
        $members->insert([
            // Group 1: Students 1,2,3 (leader = 1)
            ['group_member_id' => 1, 'group_id' => 1, 'student_id' => 1, 'is_leader' => 1, 'added_at' => date('Y-m-d H:i:s')],
            ['group_member_id' => 2, 'group_id' => 1, 'student_id' => 2, 'is_leader' => 0, 'added_at' => date('Y-m-d H:i:s')],
            ['group_member_id' => 3, 'group_id' => 1, 'student_id' => 3, 'is_leader' => 0, 'added_at' => date('Y-m-d H:i:s')],
            // Group 2: Students 4,5,6 (leader = 4)
            ['group_member_id' => 4, 'group_id' => 2, 'student_id' => 4, 'is_leader' => 1, 'added_at' => date('Y-m-d H:i:s')],
            ['group_member_id' => 5, 'group_id' => 2, 'student_id' => 5, 'is_leader' => 0, 'added_at' => date('Y-m-d H:i:s')],
            ['group_member_id' => 6, 'group_id' => 2, 'student_id' => 6, 'is_leader' => 0, 'added_at' => date('Y-m-d H:i:s')],
        ])->save();

        // ---------------------------
        // 4. GROUP PANEL
        // ---------------------------
        $panel = $this->table('group_panel');
        $panel->truncate();
        $panel->insert([
            // Group 1: adviser = prof1 (7), chair = prof2 (8), critic = prof3 (9)
            ['panel_id' => 1, 'group_id' => 1, 'professor_id' => 7, 'role' => 'adviser', 'added_at' => date('Y-m-d H:i:s')],
            ['panel_id' => 2, 'group_id' => 1, 'professor_id' => 8, 'role' => 'chair',   'added_at' => date('Y-m-d H:i:s')],
            ['panel_id' => 3, 'group_id' => 1, 'professor_id' => 9, 'role' => 'critic',  'added_at' => date('Y-m-d H:i:s')],
            // Group 2: adviser = prof2 (8) [overlap], chair = prof4 (10), critic = prof5 (11)
            ['panel_id' => 4, 'group_id' => 2, 'professor_id' => 8, 'role' => 'adviser', 'added_at' => date('Y-m-d H:i:s')],
            ['panel_id' => 5, 'group_id' => 2, 'professor_id' => 10, 'role' => 'chair',   'added_at' => date('Y-m-d H:i:s')],
            ['panel_id' => 6, 'group_id' => 2, 'professor_id' => 11, 'role' => 'critic',  'added_at' => date('Y-m-d H:i:s')],
        ])->save();

        // ---------------------------
        // 5. PROFESSOR AVAILABILITY (overlapping slots)
        // ---------------------------
        $availability = $this->table('professor_availability');
        $availability->truncate();

        // Common date: 2026-08-10 and 2026-08-11
        $slots = [
            ['available_date' => '2026-08-10', 'start_time' => '09:00:00', 'end_time' => '12:00:00'],
            ['available_date' => '2026-08-10', 'start_time' => '13:00:00', 'end_time' => '16:00:00'],
            ['available_date' => '2026-08-11', 'start_time' => '10:00:00', 'end_time' => '12:00:00'],
        ];

        $availData = [];
        $availId = 1;
        foreach ([7,8,9,10,11] as $profId) {
            foreach ($slots as $slot) {
                $availData[] = [
                    'availability_id' => $availId++,
                    'professor_id'    => $profId,
                    'available_date'  => $slot['available_date'],
                    'start_time'      => $slot['start_time'],
                    'end_time'        => $slot['end_time'],
                    'status'          => 'available',
                    'created_at'      => date('Y-m-d H:i:s'),
                ];
            }
        }
        $availability->insert($availData)->save();

        // ---------------------------
        // 6. SCHEDULE REQUESTS
        // ---------------------------
        $requests = $this->table('schedule_requests');
        $requests->truncate();
        $requests->insert([
            [
                'request_id'   => 1,
                'group_id'     => 1,
                'requested_by' => 1, // Student One
                'defense_date' => '2026-08-10',
                'start_time'   => '10:00:00',
                'end_time'     => '12:00:00',
                'status'       => 'approved',
                'requested_at' => date('Y-m-d H:i:s'),
                'finalized_at' => date('Y-m-d H:i:s'),
            ],
            [
                'request_id'   => 2,
                'group_id'     => 2,
                'requested_by' => 4, // Student Four
                'defense_date' => '2026-08-10',
                'start_time'   => '10:00:00',
                'end_time'     => '12:00:00',
                'status'       => 'approved',
                'requested_at' => date('Y-m-d H:i:s'),
                'finalized_at' => date('Y-m-d H:i:s'),
            ],
        ])->save();

        // ---------------------------
        // 7. SCHEDULE APPROVALS
        // ---------------------------
        $approvals = $this->table('schedule_approvals');
        $approvals->truncate();

        // For request 1: professors 7,8,9 approve
        $approvalData = [];
        $approvalId = 1;
        foreach ([7,8,9] as $profId) {
            $role = '';
            if ($profId == 7) $role = 'adviser';
            elseif ($profId == 8) $role = 'chair';
            elseif ($profId == 9) $role = 'critic';
            $approvalData[] = [
                'approval_id'  => $approvalId++,
                'request_id'   => 1,
                'professor_id' => $profId,
                'role'         => $role,
                'status'       => 'approved',
                'remarks'      => 'Approved',
                'responded_at' => date('Y-m-d H:i:s'),
            ];
        }
        // For request 2: professors 8,10,11 approve
        foreach ([8,10,11] as $profId) {
            $role = '';
            if ($profId == 8) $role = 'adviser';
            elseif ($profId == 10) $role = 'chair';
            elseif ($profId == 11) $role = 'critic';
            $approvalData[] = [
                'approval_id'  => $approvalId++,
                'request_id'   => 2,
                'professor_id' => $profId,
                'role'         => $role,
                'status'       => 'approved',
                'remarks'      => 'Approved',
                'responded_at' => date('Y-m-d H:i:s'),
            ];
        }
        $approvals->insert($approvalData)->save();

        // ---------------------------
        // 8. DEFENSE SCHEDULES
        // ---------------------------
        $schedules = $this->table('defense_schedules');
        $schedules->truncate();
        $schedules->insert([
            [
                'schedule_id'  => 1,
                'group_id'     => 1,
                'request_id'   => 1,
                'defense_date' => '2026-08-10',
                'start_time'   => '10:00:00',
                'end_time'     => '12:00:00',
                'venue'        => 'Room 101',
                'status'       => 'scheduled',
                'created_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'schedule_id'  => 2,
                'group_id'     => 2,
                'request_id'   => 2,
                'defense_date' => '2026-08-10',
                'start_time'   => '10:00:00',
                'end_time'     => '12:00:00',
                'venue'        => 'Room 102',
                'status'       => 'scheduled',
                'created_at'   => date('Y-m-d H:i:s'),
            ],
        ])->save();

        // Re-enable foreign key checks
        $this->execute('SET FOREIGN_KEY_CHECKS=1');
    }
}