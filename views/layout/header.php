<?php
require_once __DIR__ . '/../../db/bootstrap.php';
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