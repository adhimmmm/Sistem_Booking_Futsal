<?php
// admin/dashboard.php (final)
session_start();
include '../includes/db.php';
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: login.php");
    exit;
}

// handle actions (validate/pending/delete)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] === 'valid') {
        mysqli_query($conn, "UPDATE booking SET status='valid' WHERE id = $id");
        // add to jadwal_terblokir (optional)
        $b = mysqli_fetch_assoc(mysqli_query($conn, "SELECT lapangan_id, tanggal_booking, jam_mulai, jam_selesai FROM booking WHERE id=$id"));
        if ($b) {
            $safe_lap = intval($b['lapangan_id']);
            $safe_tgl = mysqli_real_escape_string($conn, $b['tanggal_booking']);
            $safe_jm = mysqli_real_escape_string($conn, $b['jam_mulai']);
            $safe_js = mysqli_real_escape_string($conn, $b['jam_selesai']);
            mysqli_query($conn, "INSERT INTO jadwal_terblokir (lapangan_id, tanggal, jam_mulai, jam_selesai, booking_id) VALUES ($safe_lap, '$safe_tgl', '$safe_jm', '$safe_js', $id)");
        }
    } elseif ($_GET['action'] === 'pending') {
        mysqli_query($conn, "UPDATE booking SET status='pending' WHERE id = $id");
    } elseif ($_GET['action'] === 'delete') {
        // optionally delete bukti file
        $bk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT bukti_pembayaran FROM booking WHERE id=$id"));
        if ($bk && !empty($bk['bukti_pembayaran'])) {
            $f = __DIR__ . '/../' . $bk['bukti_pembayaran'];
            if (file_exists($f)) @unlink($f);
        }
        mysqli_query($conn, "DELETE FROM booking WHERE id = $id");
        mysqli_query($conn, "DELETE FROM jadwal_terblokir WHERE booking_id = $id");
    }
    header("Location: dashboard.php?ok=" . $_GET['action']);
    exit;
}

// filters
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';
$where = "";
if (in_array($statusFilter, ['pending', 'valid', 'ditolak'])) {
    $where = "WHERE b.status = '" . mysqli_real_escape_string($conn, $statusFilter) . "'";
}

$tot_pending = intval(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM booking WHERE status='pending'"))['c']);
$tot_valid   = intval(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM booking WHERE status='valid'"))['c']);
$tot_all     = intval(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM booking"))['c']);

$sql = "SELECT b.*, l.nama_lapangan FROM booking b LEFT JOIN lapangan l ON b.lapangan_id = l.id $where ORDER BY b.created_at DESC";
$res = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Admin Dashboard - Futsal Booking</title>
    <link rel="stylesheet" href="assets_admin/style.css">
</head>

<body>
    <div class="admin-top">
        <div class="brand">Admin Panel</div>
        <div class="actions">
            <a href="../index.php">Lihat Website</a>
            <a href="lapangan_list.php">Lapangan</a>
            <a href="logout.php" class="btn-danger">Logout</a>
        </div>
    </div>

    <div class="admin-container">
        <h1>Dashboard</h1>

        <?php if (isset($_GET['ok'])): ?>
            <?php if ($_GET['ok'] == 'valid'): ?>
                <div class="alert" style="background:var(--success-color);">Booking berhasil divalidasi.</div>
            <?php elseif ($_GET['ok'] == 'delete'): ?>
                <div class="alert">Booking berhasil dihapus.</div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="stats">
            <div class="stat">Pending<br><strong><?= $tot_pending ?></strong></div>
            <div class="stat">Valid<br><strong><?= $tot_valid ?></strong></div>
            <div class="stat">Total Booking<br><strong><?= $tot_all ?></strong></div>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;margin:12px 0;">
            <h2>Daftar Booking</h2>
            <div class="filter-links">
                <a href="dashboard.php?status=all">Semua</a> |
                <a href="dashboard.php?status=pending">Pending</a> |
                <a href="dashboard.php?status=valid">Valid</a> |
                <a href="dashboard.php?status=ditolak">Ditolak</a>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>WA</th>
                    <th>Lapangan</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Bukti</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1;
                while ($r = mysqli_fetch_assoc($res)): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($r['nama_pemesan']) ?></td>
                        <td><?= htmlspecialchars($r['nomor_wa']) ?></td>
                        <td><?= htmlspecialchars($r['nama_lapangan']) ?></td>
                        <td><?= htmlspecialchars($r['tanggal_booking']) ?></td>
                        <td><?= htmlspecialchars($r['jam_mulai'] . ' - ' . $r['jam_selesai']) ?></td>
                        <td>Rp <?= number_format($r['total_harga']) ?></td>
                        <td>
                            <?php if ($r['status'] == 'pending'): ?>
                                <span class="badge badge-pending">Pending</span>
                            <?php elseif ($r['status'] == 'valid'): ?>
                                <span class="badge badge-valid">Valid</span>
                            <?php else: ?>
                                <span class="badge badge-reject"><?= htmlspecialchars($r['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($r['bukti_pembayaran']) && file_exists(__DIR__ . '/../' . $r['bukti_pembayaran'])): ?>
                                <a class="preview-btn" onclick="openModal('../<?= $r['bukti_pembayaran'] ?>')">Lihat</a>
                            <?php else: echo '-';
                            endif; ?>
                        </td>
                        <td class="action-group">
                            <a class="btn-sm" style="background:var(--success-color);" href="dashboard.php?action=valid&id=<?= $r['id'] ?>" onclick="return confirm('Setujui booking?')">Valid</a>
                            <a class="btn-sm" style="background:var(--warning-color);" href="dashboard.php?action=pending&id=<?= $r['id'] ?>">Pending</a>
                            <a class="btn-sm btn-danger" href="dashboard.php?action=delete&id=<?= $r['id'] ?>" onclick="return confirm('Hapus booking ini?')">Hapus</a>

                            <a class="btn-sm" style="background:var(--primary-color);" target="_blank" href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $r['nomor_wa']) ?>?text=Halo%20<?= rawurlencode($r['nama_pemesan']) ?>%2C%20mengenai%20booking%20anda%20pada%20<?= rawurlencode($r['tanggal_booking']) ?>%20<?= rawurlencode($r['jam_mulai'] . '-' . $r['jam_selesai']) ?>">WA</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div id="modal" class="modal" onclick="closeModal()">
        <div class="modal-content" onclick="event.stopPropagation()">
            <img id="modal-img" src="" alt="Bukti">
            <div style="margin-top:10px;">
                <button class="close" onclick="closeModal()">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function openModal(src) {
            document.getElementById('modal-img').src = src;
            document.getElementById('modal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }
    </script>

</body>

</html>