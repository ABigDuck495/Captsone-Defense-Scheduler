<?php

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if(!isset($_SESSION['user_id'])) {
        header('Location: https://youtu.be/dQw4w9WgXcQ?si=GZkeE2QC83hRqpAt');
        exit;
    }