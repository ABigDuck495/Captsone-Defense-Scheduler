<?php
require_once 'Model.php';
// use Model;
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
    public function requestsForProfessor(int $professorId, ?string $status = null): array
    {
        $sql = "
            SELECT sr.*, sa.status as approval_status, sa.remarks, sa.professor_id, u.full_name as professor_name
            FROM schedule_requests sr
            JOIN schedule_approvals sa ON sr.request_id = sa.request_id
            JOIN users u ON sa.professor_id = u.user_id
            WHERE sa.professor_id = :professor_id
        ";
        $params = ['professor_id' => $professorId];
        if ($status) {
            $sql .= " AND sa.status = :status";
            $params['status'] = $status;
        }
        $sql .= " ORDER BY sr.requested_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        return $this->groupRequests($rows);
    }

    public function requestsForGroup(int $groupId): array
    {
        $sql = "
            SELECT sr.*, sa.status as approval_status, sa.remarks, u.full_name as professor_name
            FROM schedule_requests sr
            LEFT JOIN schedule_approvals sa ON sr.request_id = sa.request_id
            LEFT JOIN users u ON sa.professor_id = u.user_id
            WHERE sr.group_id = :group_id
            ORDER BY sr.requested_at DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['group_id' => $groupId]);
        $rows = $stmt->fetchAll();
        return $this->groupRequests($rows);
    }

    private function groupRequests(array $rows): array
    {
        $requests = [];
        foreach ($rows as $row) {
            $rid = $row['request_id'];
            if (!isset($requests[$rid])) {
                $requests[$rid] = [
                    'request_id'   => $rid,
                    'group_id'     => $row['group_id'],
                    'defense_date' => $row['defense_date'],
                    'start_time'   => $row['start_time'],
                    'end_time'     => $row['end_time'],
                    'status'       => $row['status'],
                    'remarks'      => $row['remarks'],
                    'approvals'    => []
                ];
            }
            if (isset($row['professor_name'])) {
                $requests[$rid]['approvals'][] = [
                    'professor_name' => $row['professor_name'],
                    'status'         => $row['approval_status'],
                    'remarks'        => $row['remarks']
                ];
            }
        }
        return array_values($requests);
    }
}
 
