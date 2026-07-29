<?php

use GroupPanel;
 
require_once __DIR__ . '/../../db/bootstrap.php';
header('Content-Type: application/json');
 
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}
 
$data = json_decode(file_get_contents('php://input'), true) ?? [];
 
(new GroupPanel())->create([
    'group_id'     => $data['group_id'] ?? null,
    'professor_id' => $data['professor_id'] ?? null,
    'role'         => $data['role'] ?? null, // adviser | chair | critic
]);
 
echo json_encode(['success' => true]);
 
