<?php

use ProfessorAvailability;
require_once __DIR__ . '/../../db/bootstrap.php';
require_once __DIR__ . '/../../Classes/ProfessorAvailability.php';
header('Content-Type: application/json');

$groupId = (int)($_GET['group_id'] ?? 0);
$year = (int)($_GET['year'] ?? date('Y'));
$month = (int)($_GET['month'] ?? date('m'));

if (!$groupId) {
    echo json_encode(['success' => false, 'message' => 'Missing group_id']);
    exit;
}

$avail = new ProfessorAvailability();
$slots = $avail->findOverlapsForGroupInMonth($groupId, $year, $month);
echo json_encode(['success' => true, 'slots' => $slots]);