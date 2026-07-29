<?php
require_once 'Model.php';
use Model;
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
}
 
