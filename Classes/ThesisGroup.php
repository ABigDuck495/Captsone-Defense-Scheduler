<?php
require_once 'Model.php';
// use Model;
class ThesisGroup extends Model
{
    protected string $table = 'thesis_groups';
    protected string $primaryKey = 'group_id';
 
    public function finalize(int $groupId): bool
    {
        return $this->update($groupId, [
            'status'       => 'finalized',
            'finalized_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
 
