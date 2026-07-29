<?php

use ProfessorAvailability;
require_once __DIR__ . '/../../db/bootstrap.php';
require_once __DIR__ . '/../../Classes/ProfessorAvailability.php';
header('Content-Type: application/json');

if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'professor') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$professorId = (int)($_GET['professor_id'] ?? $_SESSION['user_id']);
$year = (int)($_GET['year'] ?? date('Y'));
$month = (int)($_GET['month'] ?? date('m'));

$avail = new ProfessorAvailability();
$slots = $avail->getForMonth($professorId, $year, $month);
echo json_encode(['success' => true, 'slots' => $slots]);