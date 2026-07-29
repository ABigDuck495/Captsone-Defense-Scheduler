
<?php
require_once 'Model.php';
use Model;
class ScheduleRequest extends Model
{
    protected string $table = 'schedule_requests';
    protected string $primaryKey = 'request_id';
}
 
