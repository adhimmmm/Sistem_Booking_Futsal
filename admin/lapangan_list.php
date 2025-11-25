<?php
include '../includes/db.php';
include '../includes/session.php';

$upload_dir = '../uploads/lapangan/';

$success_msg = '';
if (isset($_GET['ok'])) {
    if ($_GET['ok'] === 'tambah') $success_msg = 'Lapangan baru berhasil ditambahkan.';
    if ($_GET['ok'] === '1') $success_msg = 'Lapangan berhasil diupdate.';
    if ($_GET['ok'] === 'delete') $success_msg = 'Lapangan berhasil dihapus.';
}

// HAPUS DATA
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Ambil nama foto
    $foto_q = mysqli_query($conn, "SELECT foto FROM lapangan WHERE id=$id");
    $foto = mysqli_fetch_assoc($foto_q)['foto'];

    if ($foto && file_exists($upload_dir . $foto)) {
        unlink($upload_dir . $foto);
    }

    mysqli_query($conn, "DELETE FROM lapangan WHERE id = $id");
    header("Location: lapangan_list.php?ok=delete");
    exit;
}

$res = mysqli_query($conn, "SELECT * FROM lapangan ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Lapangan - Admin</title>
    <link rel="stylesheet" href="assets_admin/style.css">
</head>

<body>
<?php include 'nav_admin.php'; ?>
<div class="admin-container">

    <h2>Data Lapangan 
        <a class="btn-sm float-right" href="lapangan_tambah.php">Tambah Lapangan</a>
    </h2>

    <?php if ($success_msg): ?>
        <div class="alert" style="background:var(--success-color);"><?= $success_msg ?></div>
    <?php endif; ?>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Foto</th>
                <th>Nama</th>
                <th>Harga/jam</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>

        <?php $i = 1; while ($r = mysqli_fetch_assoc($res)): ?>
            <tr>
                <td><?= $i++ ?></td>

                <td>
                    <?php if ($r['foto']): ?>
                        <img src="../uploads/lapangan/<?= $r['foto'] ?>" width="70">
                    <?php else: ?>
                        <span style="color:#aaa;">Tidak ada</span>
                    <?php endif; ?>
                </td>

                <td><?= htmlspecialchars($r['nama_lapangan']) ?></td>

                <td>Rp <?= number_format($r['harga_per_jam']) ?></td>

                <td>
                    <?php if ($r['status'] == 'aktif'): ?>
                        <span class="badge badge-valid">Aktif</span>
                    <?php else: ?>
                        <span class="badge badge-reject">Nonaktif</span>
                    <?php endif; ?>
                </td>

                <td>
                    <a href="lapangan_edit.php?id=<?= $r['id'] ?>" class="btn-sm">Edit</a>
                    <a href="?delete=<?= $r['id'] ?>" onclick="return confirm('Hapus lapangan?')" class="btn-sm btn-danger">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>

        </tbody>
    </table>

</div>

</body>

</html>
