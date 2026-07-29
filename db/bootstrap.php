<?php

$sessionPath = __DIR__ . '/../storage/sessions';
if (!is_dir($sessionPath) && !@mkdir($sessionPath, 0777, true) && !is_dir($sessionPath)) {
    // Could not create it — fall back to PHP's default session path silently.
} else {
    session_save_path($sessionPath);
}

session_start();

require_once __DIR__ . '/../Classes/Model.php';
require_once __DIR__ . '/../Classes/User.php';
require_once __DIR__ . '/database.php';

$database = new Database();
$pdo = $database->getConnection();
$db = $pdo;
$conn = $pdo;