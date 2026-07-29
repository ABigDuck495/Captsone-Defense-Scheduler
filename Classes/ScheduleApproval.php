<?php

class ScheduleApproval extends Model
{
    protected string $table = 'schedule_approvals';
    protected string $primaryKey = 'approval_id';

    public function allApproved(int $requestId): bool
    {
        $rows = $this->where('request_id', $requestId);

        if (empty($rows)) {
            return false;
        }

        foreach ($rows as $row) {
            if ($row['status'] !== 'approved') {
                return false;
            }
        }

        return true;
    }

    /** For a student's "My Requests" tab - who on the panel approved/rejected/pending. */
    public function withProfessorNames(int $requestId): array
    {
        $sql = "
            SELECT sa.*, u.full_name AS professor_name
            FROM schedule_approvals sa
            JOIN users u ON u.user_id = sa.professor_id
            WHERE sa.request_id = :request_id
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['request_id' => $requestId]);
        return $stmt->fetchAll();
    }

    /** For a professor's "Requests" tab - every approval row assigned to them, with request details. */
    public function pendingForProfessor(int $professorId): array
    {
        $sql = "
            SELECT sa.*, sr.group_id, sr.defense_date, sr.start_time, sr.end_time, sr.status AS request_status
            FROM schedule_approvals sa
            JOIN schedule_requests sr ON sr.request_id = sa.request_id
            WHERE sa.professor_id = :professor_id
            ORDER BY sr.defense_date, sr.start_time
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['professor_id' => $professorId]);
        return $stmt->fetchAll();
    }
}