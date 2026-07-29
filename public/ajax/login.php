
<?php

use User;

require_once __DIR__ . '/../../db/bootstrap.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true) ?? [];

$email    = $data['email'] ?? '';
$password = $data['password'] ?? '';

$dbConnection = $db ?? $pdo ?? $conn ?? null;
$user = (new User($dbConnection))->findByEmail($email);

if (!$user || !password_verify($password, $user['password_hash'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    exit;
}

$_SESSION['user_id'] = $user['user_id'];
$_SESSION['role']    = $user['role'];

$redirect = $user['role'] === 'professor'
    ? '../dashboard/professor.php'
    : '../dashboard/student.php';

echo json_encode([
    'success' => true,
    'role' => $user['role'],
    'redirect' => $redirect
]);