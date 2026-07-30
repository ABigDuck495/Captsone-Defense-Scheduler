<?php
if (session_status() === PHP_SESSION_NONE) {
    session_save_path(__DIR__ . '/../../storage/sessions');
    session_start();
}

// Clear all session data
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
session_destroy();

if (!defined('TSS_BASE_URL')) {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $segments = array_values(array_filter(explode('/', trim($path, '/')), 'strlen'));
    $base = '/';
    foreach ($segments as $seg) {
        if (in_array($seg, ['auth', 'dashboard', 'views', 'public'], true)) {
            break;
        }
        $base = '/' . $seg . '/';
    }
    $loginUrl = $base . 'views/auth/login.php';
} else {
    $loginUrl = TSS_BASE_URL . 'views/auth/login.php';
}

header('Location: ' . $loginUrl);
exit;