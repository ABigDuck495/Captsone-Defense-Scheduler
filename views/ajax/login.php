<?php
require_once __DIR__ . '/../../db/bootstrap.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true) ?? [];
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email and password required']);
    exit;
}

$userModel = new User();
$user = $userModel->authenticate($email, $password);

if (!$user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    exit;
}

$_SESSION['user_id'] = $user['user_id'];
$_SESSION['role'] = $user['role'];

$redirect = ($user['role'] === 'professor')
    ? TSS_BASE_URL . 'views/dashboard/professor.php'
    : TSS_BASE_URL . 'views/dashboard/student.php';

echo json_encode(['success' => true, 'role' => $user['role'], 'redirect' => $redirect]);