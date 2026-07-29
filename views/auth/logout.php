<?php
session_start();
$_SESSION = [];
session_destroy();
$basePath = '/';
if (isset($_SERVER['REQUEST_URI'])) {
    $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
    $segments = array_values(array_filter(explode('/', trim($requestPath, '/')), 'strlen'));
    $basePath = '/';
    foreach ($segments as $segment) {
        if (in_array($segment, ['auth', 'dashboard', 'views', 'public'], true)) {
            break;
        }
        $basePath = '/' . $segment . '/';
    }
}
header('Location: ' . $basePath . 'views/auth/login.php');
exit;