<?php

require_once __DIR__ . '/../../db/bootstrap.php';
require_once __DIR__ . '/../../Classes/ProfessorAvailability.php';
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$role = $_SESSION['role'] ?? '';
$requestedProfessorId = isset($_GET['professor_id']) ? (int) $_GET['professor_id'] : null;

if ($role === 'professor') {
    $professorId = $requestedProfessorId ?: $_SESSION['user_id'];
} elseif ($role === 'student') {
    if (!$requestedProfessorId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing professor_id']);
        exit;
    }
    $professorId = $requestedProfessorId;
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$year = (int)($_GET['year'] ?? date('Y'));
$month = (int)($_GET['month'] ?? date('m'));

$avail = new ProfessorAvailability();
$slots = $avail->getForMonth($professorId, $year, $month);
echo json_encode(['success' => true, 'slots' => $slots]);