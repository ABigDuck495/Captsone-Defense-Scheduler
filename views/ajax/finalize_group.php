<?php


require_once __DIR__ . '/../../db/bootstrap.php';
header('Content-Type: application/json');

if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true) ?? [];
$groupId = (int) ($data['group_id'] ?? 0);

(new ThesisGroup())->finalize($groupId);

echo json_encode(['success' => true]);