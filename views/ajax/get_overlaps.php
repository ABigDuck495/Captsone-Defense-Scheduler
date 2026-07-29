<?php

use ProfessorAvailability;

require_once __DIR__ . '/../../db/bootstrap.php';
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$groupId = (int) ($_GET['group_id'] ?? 0);
$slots = (new ProfessorAvailability())->findOverlapsForGroup($groupId);

echo json_encode(['success' => true, 'slots' => $slots]);