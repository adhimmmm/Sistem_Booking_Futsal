<?php
include '../includes/db.php';
include '../includes/session.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lapangan']);
    $des  = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga = intval($_POST['harga_per_jam']);
    $status = in_array($_POST['status'], ['aktif', 'nonaktif']) ? $_POST['status'] : 'aktif';

    $sql = "INSERT INTO lapangan (nama_lapangan, deskripsi, harga_per_jam, status, created_at) VALUES ('$nama', '$des', $harga, '$status', NOW())";
    if (mysqli_query($conn, $sql)) {
        header("Location: lapangan_list.php");
        exit;
    } else $msg = "Gagal: " . mysqli_error($conn);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Tambah Lapangan - Admin</title>
    <link rel="stylesheet" href="assets_admin/style.css">
</head>

<body>
    <?php include 'nav_admin.php'; ?>
    <div class="admin-container">
        <?php if ($msg): ?><div class="alert"><?= $msg ?></div><?php endif; ?>
        <form method="POST">
            <label>Nama Lapangan
                <input type="text" name="nama_lapangan" required>
            </label>
            <label>Deskripsi
                <textarea name="deskripsi" rows="4"></textarea>
            </label>
            <label>Harga per jam (angka)
                <input type="number" name="harga_per_jam" required>
            </label>
            <label>Status
                <select name="status">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </label>
            <button class="btn" type="submit">Simpan</button>
        </form>
    </div>
</body>

</html>