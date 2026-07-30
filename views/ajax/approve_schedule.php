<?php


require_once __DIR__ . '/../../db/bootstrap.php';
header('Content-Type: application/json');

if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'professor') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$data        = json_decode(file_get_contents('php://input'), true) ?? [];
$requestId   = (int) ($data['request_id'] ?? 0);
$professorId = $_SESSION['user_id'];
$decision    = $data['decision'] ?? ''; // 'approved' | 'rejected'

$approvalModel = new ScheduleApproval();

foreach ($approvalModel->where('request_id', $requestId) as $row) {
    if ((int) $row['professor_id'] === (int) $professorId) {
        $approvalModel->update($row['approval_id'], [
            'status'       => $decision,
            'responded_at' => date('Y-m-d H:i:s'),
        ]);
    }
}

$requestModel = new ScheduleRequest();

if ($decision === 'rejected') {
    $requestModel->update($requestId, ['status' => 'rejected']);
} elseif ($approvalModel->allApproved($requestId)) {
    // Every panel member has approved - confirm the defense
    $request = $requestModel->find($requestId);
    $requestModel->update($requestId, ['status' => 'approved']);

    (new DefenseSchedule())->create([
        'group_id'     => $request['group_id'],
        'request_id'   => $requestId,
        'defense_date' => $request['defense_date'],
        'start_time'   => $request['start_time'],
        'end_time'     => $request['end_time'],
        'status'       => 'scheduled',
    ]);
}

echo json_encode(['success' => true]);