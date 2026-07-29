<?php

use User;

require_once __DIR__ . '/../../Classes/User.php';
require_once __DIR__ . '/../../db/bootstrap.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true) ?? [];

$email = trim((string)($data['email'] ?? ''));
$password = trim((string)($data['password'] ?? ''));

$dbConnection = $db ?? $pdo ?? $conn ?? null;
$user = null;
if ($email !== '') {
    $user = (new User($dbConnection))->authenticate($email, $password);
}

if (!$user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    exit;
}

$_SESSION['user_id'] = $user['user_id'];
$_SESSION['role']    = $user['role'];

$__tssRequestPath = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '/';
$__tssBaseSegments = [];
foreach (array_values(array_filter(explode('/', trim($__tssRequestPath, '/')), 'strlen')) as $segment) {
    if (in_array($segment, ['auth', 'dashboard', 'views', 'public'], true)) {
        break;
    }
    $__tssBaseSegments[] = $segment;
}
$__tssBaseUrl = '/' . implode('/', $__tssBaseSegments) . '/';
if ($__tssBaseUrl === '//') {
    $__tssBaseUrl = '/';
}

$redirect = $user['role'] === 'professor'
    ? $__tssBaseUrl . 'views/dashboard/professor.php'
    : $__tssBaseUrl . 'views/dashboard/student.php';

echo json_encode([
    'success' => true,
    'role' => $user['role'],
    'redirect' => $redirect
]);