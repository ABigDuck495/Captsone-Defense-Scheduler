<?php
 require_once 'Model.php';
use Model;
class GroupPanel extends Model
{
    protected string $table = 'group_panel';
    protected string $primaryKey = 'panel_id';
 
    public function panelOfGroup(int $groupId): array
    {
        return $this->where('group_id', $groupId);
    }
}
 
