<?php
if (!isset($__tssBaseUrl)) {
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

    session_start();

    if (isset($_SESSION['user_id'])) {
        $userRole = $_SESSION['role'] ?? '';

        if ($userRole === 'professor') {
            $dashboardPath = $__tssBaseUrl . 'views/dashboard/professor.php';
        } elseif ($userRole === 'student') {
            $dashboardPath = $__tssBaseUrl . 'views/dashboard/student.php';
        } else {
            $dashboardPath = $__tssBaseUrl . 'views/auth/login.php';
        }

        header('Location: ' . $dashboardPath);
        exit;
    } else {
        // Only redirect if NOT already on login.php
        if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
            header('Location: ' . $__tssBaseUrl . 'views/auth/login.php');
            exit;
        }
        // If you're on login.php and not logged in, just stay there
    }


}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thesis Scheduling System</title>
    <link rel="stylesheet" href="<?= $__tssBaseUrl ?>public/assets/vendor/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $__tssBaseUrl ?>public/assets/css/custom.css">
    <script>
        window.TSS_BASE_URL = <?= json_encode($__tssBaseUrl) ?>;
    </script>
</head>
<body>