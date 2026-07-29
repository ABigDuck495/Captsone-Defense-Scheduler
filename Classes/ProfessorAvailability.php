
<?php
 require_once 'Model.php';
use Model;
class ProfessorAvailability extends Model
{
    protected string $table = 'professor_availability';
    protected string $primaryKey = 'availability_id';
 
    public function findOverlapsForGroup(int $groupId): array
    {
        $sql = "
            SELECT pa.available_date, pa.start_time, pa.end_time
            FROM professor_availability pa
            JOIN group_panel gp ON gp.professor_id = pa.professor_id
            WHERE gp.group_id = :group_id
              AND pa.status = 'available'
            GROUP BY pa.available_date, pa.start_time, pa.end_time
            HAVING COUNT(DISTINCT pa.professor_id) = (
                SELECT COUNT(*) FROM group_panel WHERE group_id = :group_id
            )
            ORDER BY pa.available_date, pa.start_time
        ";
 
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['group_id' => $groupId]);
        return $stmt->fetchAll();
    }
}
 
