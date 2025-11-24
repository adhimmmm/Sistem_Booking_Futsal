<?php
// includes/db.php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "futsal_db"; // ganti sesuai nama database

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
