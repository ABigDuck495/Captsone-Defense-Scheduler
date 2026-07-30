<?php
require_once __DIR__ . '/../../Classes/ProfessorAvailability.php';
require_once __DIR__ . '/../../db/bootstrap.php';
header('Content-Type: application/json');
 
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'professor') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}
 
$data = json_decode(file_get_contents('php://input'), true) ?? [];
 
(new ProfessorAvailability())->create([
    'professor_id'   => $_SESSION['user_id'],
    'available_date' => $data['date'] ?? null,
    'start_time'     => $data['start_time'] ?? null,
    'end_time'       => $data['end_time'] ?? null,
    'status'         => 'available',
]);
 
echo json_encode(['success' => true]);