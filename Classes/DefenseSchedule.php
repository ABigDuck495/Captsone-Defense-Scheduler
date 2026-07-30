<?php

class DefenseSchedule extends Model
{
    protected string $table = 'defense_schedules';
    protected string $primaryKey = 'schedule_id';

    public function forGroup(int $groupId): array
    {
        return $this->where('group_id', $groupId);
    }

    public function forProfessor(int $professorId): array
    {
        $sql = "
            SELECT ds.*
            FROM defense_schedules ds
            JOIN group_panel gp ON gp.group_id = ds.group_id
            WHERE gp.professor_id = :professor_id
            GROUP BY ds.schedule_id
            ORDER BY ds.defense_date, ds.start_time
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['professor_id' => $professorId]);
        return $stmt->fetchAll();
    }
}