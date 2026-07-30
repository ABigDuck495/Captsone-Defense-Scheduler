<?php
require_once 'Model.php';
// use Model;

class ProfessorAvailability extends Model
{
    protected string $table = 'professor_availability';
    protected string $primaryKey = 'availability_id';

    public function addSlot(int $professorId, string $date, string $startTime, bool $weekly = false): void
    {
        $dates = [$date];
        if ($weekly) {
            $dt = new DateTime($date);
            $endOfMonth = new DateTime($dt->format('Y-m-t'));
            while ($dt->modify('+1 week') <= $endOfMonth) {
                $dates[] = $dt->format('Y-m-d');
            }
        }
        $sql = "INSERT IGNORE INTO professor_availability (professor_id, available_date, start_time, end_time, status)
                VALUES (?, ?, ?, DATE_ADD(?, INTERVAL 1 HOUR), 'available')";
        $stmt = $this->db->prepare($sql);
        foreach ($dates as $d) {
            $stmt->execute([$professorId, $d, $startTime, $startTime]);
        }
    }

    public function removeSlot(int $professorId, string $date, string $startTime): void
    {
        $sql = "DELETE FROM professor_availability 
                WHERE professor_id = ? AND available_date = ? AND start_time = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$professorId, $date, $startTime]);
    }

    public function getForMonth(int $professorId, int $year, int $month): array
    {
        $start = "$year-$month-01";
        $end = date('Y-m-t', strtotime($start));
        $sql = "SELECT available_date, start_time FROM professor_availability
                WHERE professor_id = ? AND available_date BETWEEN ? AND ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$professorId, $start, $end]);
        $rows = $stmt->fetchAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['available_date'] . ' ' . $row['start_time']] = 'available';
        }
        return $result;
    }

    public function findOverlapsForGroupInMonth(int $groupId, int $year, int $month): array
    {
        $start = "$year-$month-01";
        $end = date('Y-m-t', strtotime($start));
        $sql = "
            SELECT pa.available_date, pa.start_time, pa.end_time
            FROM professor_availability pa
            JOIN group_panel gp ON gp.professor_id = pa.professor_id
            WHERE gp.group_id = :group_id
              AND pa.status = 'available'
              AND pa.available_date BETWEEN :start AND :end
            GROUP BY pa.available_date, pa.start_time, pa.end_time
            HAVING COUNT(DISTINCT pa.professor_id) = (
                SELECT COUNT(*) FROM group_panel WHERE group_id = :group_id
            )
            ORDER BY pa.available_date, pa.start_time
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['group_id' => $groupId, 'start' => $start, 'end' => $end]);
        $rows = $stmt->fetchAll();
        $result = [];
        foreach ($rows as $row) {
            $key = $row['available_date'] . ' ' . $row['start_time'];
            $result[$key] = 'available';
        }
        return $result;
    }
}