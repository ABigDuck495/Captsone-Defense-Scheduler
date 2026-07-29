<?php

class GroupPanel extends Model
{
    protected string $table = 'group_panel';
    protected string $primaryKey = 'panel_id';

    public function panelOfGroup(int $groupId): array
    {
        return $this->where('group_id', $groupId);
    }

    /**
     * Replace a group's panel with exactly one adviser, one chair,
     * and one critic.
     */
    public function replacePanel(int $groupId, int $adviserId, int $chairId, int $criticId): void
    {
        $stmt = $this->db->prepare('DELETE FROM group_panel WHERE group_id = :group_id');
        $stmt->execute(['group_id' => $groupId]);

        $this->create(['group_id' => $groupId, 'professor_id' => $adviserId, 'role' => 'adviser']);
        $this->create(['group_id' => $groupId, 'professor_id' => $chairId,   'role' => 'chair']);
        $this->create(['group_id' => $groupId, 'professor_id' => $criticId,  'role' => 'critic']);
    }
}