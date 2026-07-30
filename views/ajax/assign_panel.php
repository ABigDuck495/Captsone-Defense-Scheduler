<?php

require_once __DIR__ . '/../../db/bootstrap.php';
require_once __DIR__ . '/../../Classes/GroupPanel.php';
header('Content-Type: application/json');

if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true) ?? [];

$groupId   = (int) ($data['group_id'] ?? 0);
$adviserId = (int) ($data['adviser_id'] ?? 0);
$chairId   = (int) ($data['chair_id'] ?? 0);
$criticId  = (int) ($data['critic_id'] ?? 0);

if (!$groupId || !$adviserId || !$chairId || !$criticId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing group_id or professor ids']);
    exit;
}

$ids = [$adviserId, $chairId, $criticId];
if (count(array_unique($ids)) !== 3) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Adviser, chair, and critic must be 3 different professors']);
    exit;
}

(new GroupPanel())->replacePanel($groupId, $adviserId, $chairId, $criticId);

echo json_encode(['success' => true]);