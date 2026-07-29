
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

$basePath = '/';
if (isset($_SERVER['REQUEST_URI'])) {
    $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
    $segments = array_values(array_filter(explode('/', trim($requestPath, '/')), 'strlen'));
    foreach ($segments as $segment) {
        if (in_array($segment, ['auth', 'dashboard', 'views', 'public'], true)) {
            break;
        }
        $basePath = '/' . $segment . '/';
    }
}
$redirect = $user['role'] === 'professor'
    ? $basePath . 'dashboard/professor.php'
    : $basePath . 'dashboard/student.php';

echo json_encode([
    'success' => true,
    'role' => $user['role'],
    'redirect' => $redirect
]);