<?php
// admin/login.php
session_start();
include '../includes/db.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($user === '' || $pass === '') {
        $msg = "Username & password wajib diisi.";
    } else {
        $sql = "SELECT id, username, password FROM admin WHERE username = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $user);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($res)) {
            if (password_verify($pass, $row['password'])) {
                $_SESSION['admin_logged'] = true;
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['admin_username'] = $row['username'];
                header("Location: dashboard.php");
                exit;
            } else $msg = "Password salah.";
        } else $msg = "User tidak ditemukan.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Admin Login - Futsal Booking</title>
    <link rel="stylesheet" href="assets_admin/style.css">
</head>

<body class="admin-login-body">
    <div class="admin-login-card">
        <h2>Admin Login</h2>
        <?php if ($msg): ?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <form method="POST" action="">
            <label>Username
                <input type="text" name="username" required>
            </label>
            <label>Password
                <input type="password" name="password" required>
            </label>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>

</html>