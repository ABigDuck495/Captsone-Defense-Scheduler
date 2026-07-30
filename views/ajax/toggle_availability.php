<?php

require_once __DIR__ . '/../../db/bootstrap.php';
require_once __DIR__ . '/../../Classes/ProfessorAvailability.php';
header('Content-Type: application/json');

if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'professor') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true) ?? [];
$professorId = (int)($data['professor_id'] ?? $_SESSION['user_id']);
$date = $data['date'] ?? '';
$time = $data['time'] ?? '';
$action = $data['action'] ?? 'add';
$repeat = $data['repeat'] ?? false;

if (!$date || !$time) {
    echo json_encode(['success' => false, 'message' => 'Missing date or time']);
    exit;
}

$avail = new ProfessorAvailability();
if ($action === 'add') {
    $avail->addSlot($professorId, $date, $time, $repeat === 'weekly');
} else {
    $avail->removeSlot($professorId, $date, $time);
}
echo json_encode(['success' => true]);