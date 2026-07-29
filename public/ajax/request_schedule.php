<?php

use GroupPanel;
use ScheduleApproval;
use ScheduleRequest;

require_once __DIR__ . '/../../db/bootstrap.php';
header('Content-Type: application/json');

if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$data    = json_decode(file_get_contents('php://input'), true) ?? [];
$groupId = $data['group_id'] ?? null;

$requestModel = new ScheduleRequest();
$requestId = $requestModel->create([
    'group_id'     => $groupId,
    'requested_by' => $_SESSION['user_id'],
    'defense_date' => $data['date'] ?? null,
    'start_time'   => $data['start_time'] ?? null,
    'end_time'     => $data['end_time'] ?? null,
    'status'       => 'pending',
]);

// One pending approval row per panel member (adviser, chair, every critic)
$approvalModel = new ScheduleApproval();
foreach ((new GroupPanel())->panelOfGroup($groupId) as $panelMember) {
    $approvalModel->create([
        'request_id'   => $requestId,
        'professor_id' => $panelMember['professor_id'],
        'role'         => $panelMember['role'],
        'status'       => 'pending',
    ]);
}

echo json_encode(['success' => true, 'request_id' => $requestId]);