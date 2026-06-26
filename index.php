<?php
session_start();

// Jika belum login, redirect ke login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

// Redirect ke dashboard
header("Location: dashboard.php");
exit;
?>