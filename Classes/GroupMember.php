<?php
 require_once 'Model.php';
// use Model;
class GroupMember extends Model
{
    protected string $table = 'group_members';
    protected string $primaryKey = 'group_member_id';
 
    public function membersOfGroup(int $groupId): array
    {
        return $this->where('group_id', $groupId);
    }

    public function groupOfStudent(int $studentId): ?array
    {
        $rows = $this->where('student_id', $studentId);
        return $rows[0] ?? null;
    }
}