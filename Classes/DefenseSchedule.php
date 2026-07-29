<?php
require_once 'Model.php';
use Model;
 
class DefenseSchedule extends Model
{
    protected string $table = 'defense_schedules';
    protected string $primaryKey = 'schedule_id';
}
 
