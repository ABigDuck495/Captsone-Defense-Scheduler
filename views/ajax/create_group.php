<?php

 
require_once __DIR__ . '/../../db/bootstrap.php';
header('Content-Type: application/json');
 
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}
 
$data      = json_decode(file_get_contents('php://input'), true) ?? [];
$leaderId  = $_SESSION['user_id'];
 
$groupModel = new ThesisGroup();
$groupId = $groupModel->create([
    'thesis_title' => $data['thesis_title'] ?? '',
    'status'       => 'forming',
    'created_by'   => $leaderId,
]);
 
// Leader is automatically a member of their own group
(new GroupMember())->create([
    'group_id'   => $groupId,
    'student_id' => $leaderId,
    'is_leader'  => 1,
]);
 
$_SESSION['group_id'] = $groupId;
 
echo json_encode(['success' => true, 'group_id' => $groupId]);