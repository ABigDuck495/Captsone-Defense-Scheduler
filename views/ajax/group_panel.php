<?php

class GroupPanel extends Model
{
    protected string $table = 'group_panel';
    protected string $primaryKey = 'panel_id';

    public function panelOfGroup(int $groupId): array
    {
        return $this->where('group_id', $groupId);
    }
    public function panelWithNames(int $groupId): array
    {
        $sql = "
            SELECT gp.professor_id, gp.role, u.full_name
            FROM group_panel gp
            JOIN users u ON u.user_id = gp.professor_id
            WHERE gp.group_id = :group_id
            ORDER BY FIELD(gp.role, 'adviser', 'chair', 'critic')
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['group_id' => $groupId]);
        return $stmt->fetchAll();
    }

    public function replacePanel(int $groupId, int $adviserId, int $chairId, int $criticId): void
    {
        $stmt = $this->db->prepare('DELETE FROM group_panel WHERE group_id = :group_id');
        $stmt->execute(['group_id' => $groupId]);

        $this->create(['group_id' => $groupId, 'professor_id' => $adviserId, 'role' => 'adviser']);
        $this->create(['group_id' => $groupId, 'professor_id' => $chairId,   'role' => 'chair']);
        $this->create(['group_id' => $groupId, 'professor_id' => $criticId,  'role' => 'critic']);
    }
}