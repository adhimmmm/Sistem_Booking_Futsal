<?php
// admin/dashboard.php
include '../includes/db.php';
include '../includes/session.php';

$tot_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM booking WHERE status='pending'"))['c'];
$tot_valid   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM booking WHERE status='valid'"))['c'];
$tot_all     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM booking"))['c'];

$res = mysqli_query($conn, "SELECT b.*, l.nama_lapangan FROM booking b LEFT JOIN lapangan l ON b.lapangan_id = l.id ORDER BY b.created_at DESC LIMIT 8");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Dashboard Admin - Futsal Booking</title>
    <link rel="stylesheet" href="assets_admin/style.css">
</head>

<body>
    <div class="admin-container">
        <h1>Dashboard</h1>
        <div class="stats">
            <div class="stat">Pending<br><strong><?= intval($tot_pending) ?></strong></div>
            <div class="stat">Valid<br><strong><?= intval($tot_valid) ?></strong></div>
            <div class="stat">Total Booking<br><strong><?= intval($tot_all) ?></strong></div>
        </div>

        <h3>Booking Terbaru</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Lapangan</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1;
                while ($r = mysqli_fetch_assoc($res)): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($r['nama_pemesan']) ?></td>
                        <td><?= htmlspecialchars($r['nama_lapangan']) ?></td>
                        <td><?= htmlspecialchars($r['tanggal_booking']) ?></td>
                        <td><?= htmlspecialchars($r['jam_mulai'] . ' - ' . $r['jam_selesai']) ?></td>
                        <td><?= htmlspecialchars($r['status']) ?></td>
                        <td>
                            <a class="btn-sm" href="booking_detail.php?id=<?= $r['id'] ?>">Detail</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>