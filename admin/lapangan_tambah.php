<?php
include '../includes/db.php';
include '../includes/session.php';

$msg = '';
$upload_dir = '../uploads/lapangan/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = mysqli_real_escape_string($conn, $_POST['nama_lapangan']);
    $des  = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga = intval($_POST['harga_per_jam']);
    $status = in_array($_POST['status'], ['aktif', 'nonaktif']) ? $_POST['status'] : 'aktif';

    // ---------- HANDLE FOTO ----------
    $foto_name = '';
    if (!empty($_FILES['foto']['name'])) {

        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_name = time() . '_' . rand(1000,9999) . '.' . $ext;

        move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $foto_name);
    }

    $sql = "INSERT INTO lapangan (nama_lapangan, deskripsi, foto, harga_per_jam, status, created_at) 
            VALUES ('$nama', '$des', '$foto_name', $harga, '$status', NOW())";

    if (mysqli_query($conn, $sql)) {
        header("Location: lapangan_list.php?ok=tambah");
        exit;
    } else {
        $msg = "Gagal: " . mysqli_error($conn);
    }
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
    <h2>Tambah Lapangan Baru</h2>

    <?php if ($msg): ?><div class="alert"><?= $msg ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Nama Lapangan
            <input type="text" name="nama_lapangan" required>
        </label>

        <label>Deskripsi
            <textarea name="deskripsi" rows="4"></textarea>
        </label>

        <label>Foto Lapangan
            <input type="file" name="foto" accept="image/*">
        </label>

        <label>Harga per jam
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
