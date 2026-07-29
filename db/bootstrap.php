<?php

session_start();

require_once __DIR__ . '/../Classes/Model.php';
require_once __DIR__ . '/../Classes/User.php';
require_once __DIR__ . '/database.php';

$database = new Database();
$pdo = $database->getConnection();
$db = $pdo;
$conn = $pdo;
