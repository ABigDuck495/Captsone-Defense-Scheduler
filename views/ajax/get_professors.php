<?php
require_once __DIR__ . '/../../db/bootstrap.php';
require_once __DIR__ . '/../../Classes/User.php';
header('Content-Type: application/json');

$dbConnection = $db ?? $pdo ?? $conn ?? null;
$user = new User($dbConnection);
$professors = $user->findProfessors();
echo json_encode(['success' => true, 'professors' => $professors]);