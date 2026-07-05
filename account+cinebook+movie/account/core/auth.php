<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkLogin() {
    if (empty($_SESSION['user'])) {
        header("Location: /account/login.php");
        exit;
    }
}

function checkAdmin() {
    if (empty($_SESSION['user'])) {
        header("Location: /account/login.php");
        exit;
    }

    if ($_SESSION['user']['role'] !== 'admin') {
        header("Location: /account/index.php");
        exit;
    }
}

function validateSession() {
    if (empty($_SESSION['user'])) {
        return;
    }

    if (isset($_SESSION['user']['ip']) && $_SESSION['user']['ip'] !== $_SERVER['REMOTE_ADDR']) {
        session_destroy();
        header("Location: /account/login.php");
        exit;
    }
}