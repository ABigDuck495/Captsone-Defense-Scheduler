<?php

require_once __DIR__ . '/../../db/bootstrap.php';
require_once __DIR__ . '/../../Classes/DefenseSchedule.php';
header('Content-Type: application/json');

$professorId = (int)($_GET['professor_id'] ?? 0);
$groupId = (int)($_GET['group_id'] ?? 0);
if (!$professorId && !$groupId) {
    echo json_encode(['success' => false, 'message' => 'Missing parameter']);
    exit;
}

$defense = new DefenseSchedule();
if ($professorId) {
    $booked = $defense->forProfessor($professorId);
} else {
    $booked = $defense->forGroup($groupId);
}
echo json_encode(['success' => true, 'booked' => $booked]);