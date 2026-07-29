<?php
if (!defined('TSS_BOOTSTRAP_LOADED')) {
    define('TSS_BOOTSTRAP_LOADED', true);

    // Session setup
    $sessionPath = __DIR__ . '/../storage/sessions';
    if (!is_dir($sessionPath) && !@mkdir($sessionPath, 0777, true) && !is_dir($sessionPath)) {
        // fallback
    } else {
        if (session_status() === PHP_SESSION_NONE) {
            session_save_path($sessionPath);
        }
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Base URL
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $pathSegments = array_values(array_filter(explode('/', trim($requestPath, '/')), 'strlen'));
    $baseSegments = [];
    foreach ($pathSegments as $segment) {
        if (in_array($segment, ['auth', 'dashboard', 'views', 'public', 'ajax'], true)) {
            break;
        }
        $baseSegments[] = $segment;
    }
    $__tssBaseUrl = '/' . implode('/', $baseSegments) . '/';
    if ($__tssBaseUrl === '//') {
        $__tssBaseUrl = '/';
    }
    define('TSS_BASE_URL', $__tssBaseUrl);

    // Determine current page
    $currentScript = basename($_SERVER['PHP_SELF'] ?? '');
    $currentDir = basename(dirname($_SERVER['PHP_SELF'] ?? ''));
    $isAjax = ($currentDir === 'ajax') || !empty($_SERVER['HTTP_X_REQUESTED_WITH']);
    $isLogin = ($currentDir === 'auth' && $currentScript === 'login.php');
    $isIndex = ($currentScript === 'index.php');
    $isProfessor = ($currentDir === 'dashboard' && $currentScript === 'professor.php');
    $isStudent = ($currentDir === 'dashboard' && $currentScript === 'student.php');

    $loggedIn = !empty($_SESSION['user_id']);
    $role = $_SESSION['role'] ?? '';

    // Debug: log current state
    error_log("bootstrap: loggedIn=$loggedIn, role=$role, script=$currentScript, dir=$currentDir");

    // Redirect logic
    if (!$loggedIn) {
        // Not logged in
        if (!$isLogin && !$isIndex && !$isAjax) {
            error_log("bootstrap: redirecting to login (not logged in)");
            header('Location: ' . TSS_BASE_URL . 'views/auth/login.php');
            exit;
        }
    } else {
        // Logged in
        if ($isLogin || $isIndex) {
            // Redirect to dashboard
            $target = ($role === 'professor') ? 'professor.php' : 'student.php';
            error_log("bootstrap: redirecting from public to $target");
            header('Location: ' . TSS_BASE_URL . 'views/dashboard/' . $target);
            exit;
        }
        if ($isProfessor && $role !== 'professor') {
            error_log("bootstrap: professor page but role is not professor, redirect to student");
            header('Location: ' . TSS_BASE_URL . 'views/dashboard/student.php');
            exit;
        }
        if ($isStudent && $role !== 'student') {
            error_log("bootstrap: student page but role is not student, redirect to professor");
            header('Location: ' . TSS_BASE_URL . 'views/dashboard/professor.php');
            exit;
        }
        // For any other non-AJAX page that is not allowed, redirect to dashboard
        if (!$isAjax && !$isProfessor && !$isStudent) {
            $target = ($role === 'professor') ? 'professor.php' : 'student.php';
            error_log("bootstrap: redirecting unknown page to dashboard/$target");
            header('Location: ' . TSS_BASE_URL . 'views/dashboard/' . $target);
            exit;
        }
    }

    // Load classes
    require_once __DIR__ . '/../Classes/Model.php';
    require_once __DIR__ . '/../Classes/User.php';
    require_once __DIR__ . '/../Classes/ProfessorAvailability.php';
    require_once __DIR__ . '/../Classes/DefenseSchedule.php';
    require_once __DIR__ . '/../Classes/ScheduleApproval.php';
    require_once __DIR__ . '/../Classes/ScheduleRequest.php';
    require_once __DIR__ . '/../Classes/GroupPanel.php';
    require_once __DIR__ . '/../Classes/GroupMember.php';
    require_once __DIR__ . '/../Classes/ThesisGroup.php';
    require_once __DIR__ . '/database.php';
    // require_once __DIR__ . '/../public/assets/css/custom.css';

    $database = new \Database();
    $pdo = $database->getConnection();
    $db = $pdo;
    $conn = $pdo;
}