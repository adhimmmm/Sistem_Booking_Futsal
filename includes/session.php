<?php
// includes/session.php
session_start();
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: /futsal-booking/admin/login.php");
    exit;
}
