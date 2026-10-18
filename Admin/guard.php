<?php

require_once __DIR__ . '/../User/config.php';

$isAdmin = !empty($_SESSION['is_admin']) ||
           (isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin');

if (empty($_SESSION['user_id']) || !$isAdmin) {
    header('Location: ../User/login.php');
    exit;
}
?>
