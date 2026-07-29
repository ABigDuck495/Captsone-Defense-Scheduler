<?php

use GroupMember;
use User;

require_once __DIR__ . '/../../db/bootstrap.php';
$dbConnection = $pdo ?? $db ?? null;
header('Content-Type: application/json');
 
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}
 
$data = json_decode(file_get_contents('php://input'), true) ?? [];
 
$groupId      = $data['group_id'] ?? null;
$studentEmail = $data['student_email'] ?? '';
 
$student = (new User($dbConnection))->findByEmail($studentEmail);
 
if (!$student || $student['role'] !== 'student') {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Student not found']);
    exit;
}
 
(new GroupMember())->create([
    'group_id'   => $groupId,
    'student_id' => $student['user_id'],
    'is_leader'  => 0,
]);
 
echo json_encode(['success' => true]);
 
