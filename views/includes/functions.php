<?php

use PDO;
use Database;

if (!function_exists('getConnection')) {
    function getConnection(): PDO
    {
        static $pdo = null;

        if ($pdo instanceof PDO) {
            return $pdo;
        }

        require_once __DIR__ . '/../../db/database.php';
        $database = new Database();
        $pdo = $database->getConnection();

        return $pdo;
    }
}

if (!function_exists('getDbConnection')) {
    function getDbConnection(): PDO
    {
        return getConnection();
    }
}
