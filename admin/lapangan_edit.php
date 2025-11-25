<?php
include '../includes/db.php';
include '../includes/session.php';

$upload_dir = '../uploads/lapangan/';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: lapangan_list.php"); exit; }

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = mysqli_real_escape_string($conn, $_POST['nama_lapangan']);
    $des  = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga = intval($_POST['harga_per_jam']);
    $status = in_array($_POST['status'], ['aktif', 'nonaktif']) ? $_POST['status'] : 'aktif';

    // Ambil foto lama
    $row_old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT foto FROM lapangan WHERE id=$id"));
    $foto_lama = $row_old['foto'];
    $foto_baru = $foto_lama;

    // Jika ada upload foto baru
    if (!empty($_FILES['foto']['name'])) {

        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_baru = time() . '_' . rand(1000,9999) . '.' . $ext;

        move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $foto_baru);

        // Hapus foto lama
        if ($foto_lama && file_exists($upload_dir . $foto_lama)) {
            unlink($upload_dir . $foto_lama);
        }
    }

    $sql = "UPDATE lapangan 
            SET nama_lapangan='$nama', deskripsi='$des', foto='$foto_baru',
                harga_per_jam=$harga, status='$status'
            WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        header("Location: lapangan_list.php?updated=1");
        exit;
    } else {
        $msg = "Gagal: " . mysqli_error($conn);
    }
}

$res = mysqli_query($conn, "SELECT * FROM lapangan WHERE id=$id");
$row = mysqli_fetch_assoc($res);
if (!$row) { echo "Lapangan tidak ditemukan"; exit; }

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Edit Lapangan - Admin</title>
    <link rel="stylesheet" href="assets_admin/style.css">
</head>

<body>
<?php include 'nav_admin.php'; ?>

<div class="admin-container">

<h2>Edit Lapangan: <?= htmlspecialchars($row['nama_lapangan']) ?></h2>

<?php if ($msg): ?><div class="alert"><?= $msg ?></div><?php endif; ?>
<?php if (isset($_GET['ok'])): ?>
    <div class="alert" style="background:var(--success-color);">Lapangan berhasil diupdate.</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

    <label>Nama Lapangan
        <input type="text" name="nama_lapangan" required value="<?= htmlspecialchars($row['nama_lapangan']) ?>">
    </label>

    <label>Deskripsi
        <textarea name="deskripsi" rows="4"><?= htmlspecialchars($row['deskripsi']) ?></textarea>
    </label>

    <label>Foto Saat Ini</label>
    <?php if ($row['foto']): ?>
        <img src="../uploads/lapangan/<?= $row['foto'] ?>" width="150">
    <?php else: ?>
        <p>Tidak ada foto.</p>
    <?php endif; ?>

    <label>Ganti Foto Baru (optional)
        <input type="file" name="foto" accept="image/*">
    </label>

    <label>Harga per jam
        <input type="number" name="harga_per_jam" required value="<?= $row['harga_per_jam'] ?>">
    </label>

    <label>Status
        <select name="status">
            <option value="aktif" <?= $row['status']=='aktif'?'selected':'' ?>>Aktif</option>
            <option value="nonaktif" <?= $row['status']=='nonaktif'?'selected':'' ?>>Nonaktif</option>
        </select>
    </label>

    <button class="btn" type="submit">Update</button>

</form>

</div>

</body>

</html>
