<?php
include '../includes/db.php';
include '../includes/session.php';

$success_msg = '';
if (isset($_GET['ok'])) {
    if ($_GET['ok'] === 'tambah') $success_msg = '✅ Lapangan baru berhasil ditambahkan.';
    if ($_GET['ok'] === '1') $success_msg = '✅ Lapangan berhasil diupdate.';
    if ($_GET['ok'] === 'delete') $success_msg = '🗑️ Lapangan berhasil dihapus.';
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Delete image if exists
    $img_result = mysqli_query($conn, "SELECT foto FROM lapangan WHERE id = $id");
    if ($img_row = mysqli_fetch_assoc($img_result)) {
        if (!empty($img_row['foto']) && file_exists(__DIR__ . '/../' . $img_row['foto'])) {
            @unlink(__DIR__ . '/../' . $img_row['foto']);
        }
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
    <title>Kelola Lapangan - Admin</title>
    <link rel="stylesheet" href="css_admin/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="admin-top">
        <div class="container-nav">
            <a href="dashboard.php" class="brand">ADMIN<span style="color:#64748B; font-weight:400;">PANEL</span></a>
            <div class="actions">
                <a href="dashboard.php">Dashboard</a>
                <a href="../index.php">Lihat Website</a>
                <a href="logout.php" class="btn-danger">Logout</a>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
            <h1>Kelola Lapangan</h1>
            <a class="btn" href="lapangan_tambah.php">➕ Tambah Lapangan</a>
        </div>

        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?= $success_msg ?></div>
        <?php endif; ?>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Foto</th>
                        <th>Nama Lapangan</th>
                        <th>Harga/Jam</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    while ($r = mysqli_fetch_assoc($res)):
                    ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td>
                                <?php if (!empty($r['foto']) && file_exists(__DIR__ . '/../' . $r['foto'])): ?>
                                    <img src="../<?= htmlspecialchars($r['foto']) ?>"
                                        alt="<?= htmlspecialchars($r['nama_lapangan']) ?>"
                                        style="width:80px;height:60px;object-fit:cover;border-radius:6px;box-shadow:var(--shadow-sm);">
                                <?php else: ?>
                                    <div style="width:80px;height:60px;background:var(--gray-100);border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--gray-500);">
                                        📷
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($r['nama_lapangan']) ?></strong>
                                <?php if (!empty($r['deskripsi'])): ?>
                                    <br><small style="color:var(--gray-500);"><?= substr(htmlspecialchars($r['deskripsi']), 0, 50) ?>...</small>
                                <?php endif; ?>
                            </td>
                            <td><strong style="color:var(--primary);">Rp <?= number_format($r['harga_per_jam'], 0, ',', '.') ?></strong></td>
                            <td>
                                <?php if ($r['status'] == 'aktif'): ?>
                                    <span class="badge badge-aktif">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-nonaktif">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="lapangan_edit.php?id=<?= $r['id'] ?>" class="btn-sm" style="background:var(--primary);color:white;border:none;">✏️ Edit</a>
                                    <a href="?delete=<?= $r['id'] ?>" onclick="return confirm('Hapus lapangan ini?\n\nGambar dan semua data terkait akan dihapus.')" class="btn-sm btn-danger">🗑️ Hapus</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>