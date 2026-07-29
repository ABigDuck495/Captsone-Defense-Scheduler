<?php

use ScheduleApproval;
require_once __DIR__ . '/../../db/bootstrap.php';
require_once __DIR__ . '/../../Classes/ScheduleApproval.php';
header('Content-Type: application/json');

$professorId = (int)($_GET['professor_id'] ?? 0);
$groupId = (int)($_GET['group_id'] ?? 0);
$status = $_GET['status'] ?? null;

$approval = new ScheduleApproval();
if ($professorId) {
    $requests = $approval->requestsForProfessor($professorId, $status);
} elseif ($groupId) {
    $requests = $approval->requestsForGroup($groupId);
} else {
    echo json_encode(['success' => false, 'message' => 'Missing parameter']);
    exit;
}
echo json_encode(['success' => true, 'requests' => $requests]);