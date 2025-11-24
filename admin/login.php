<?php
// admin/login.php (debug-ready)
// NOTE: Jalankan debug hanya di lingkungan development, lalu kembalikan ke versi production.

session_start();
include '../includes/db.php';

$DEBUG = isset($_GET['debug']) && $_GET['debug'] == '1';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = isset($_POST['username']) ? trim($_POST['username']) : '';
    $pass = isset($_POST['password']) ? $_POST['password'] : ''; // jangan trim dulu kalau mau lihat whitespace dari user

    if ($user === '' || $pass === '') {
        $msg = "Username & password wajib diisi.";
    } else {
        $sql = "SELECT id, username, password FROM admin WHERE username = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            $msg = "Query prepare gagal: " . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param($stmt, "s", $user);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($res)) {

                // Debug output (hanya jika debug aktif)
                if ($DEBUG) {
                    echo "<pre>DEBUG INFO\n";
                    echo "Input username : [" . $user . "]\n";
                    echo "Input password : [" . $pass . "]\n";
                    echo "Len input pw    : " . strlen($pass) . "\n";
                    echo "DB password     : [" . $row['password'] . "]\n";
                    echo "Len DB pw       : " . strlen($row['password']) . "\n";
                    // show ordinals for trailing whitespace if ada
                    echo "Input bytes     : ";
                    foreach (str_split($pass) as $c) { echo ord($c) . ' '; }
                    echo "\nDB bytes        : ";
                    foreach (str_split($row['password']) as $c) { echo ord($c) . ' '; }
                    echo "\n</pre>";
                }

                // 3 cara cek (berurut):
                // A. cek exact (case-sensitive, termasuk whitespace)
                if ($pass === $row['password']) {
                    $ok = true;
                } else {
                    // B. cek setelah trim() pada DB value dan input (mengatasi spasi accidental)
                    if (trim($pass) === trim($row['password'])) {
                        $ok = true;
                    } else {
                        // C. cek case-insensitive tanpa spasi (opsional)
                        if (strcasecmp(trim($pass), trim($row['password'])) === 0) {
                            $ok = true;
                        } else {
                            $ok = false;
                        }
                    }
                }

                if ($ok) {
                    $_SESSION['admin_logged'] = true;
                    $_SESSION['admin_id'] = $row['id'];
                    $_SESSION['admin_username'] = $row['username'];
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $msg = "Password salah.";
                }

            } else {
                $msg = "User tidak ditemukan.";
            }

            mysqli_stmt_close($stmt);
        }
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
        <?php if ($DEBUG): ?>
            <p style="color:darkred">DEBUG mode aktif. Hentikan debug setelah selesai.</p>
        <?php endif; ?>
    </div>
</body>
</html>
