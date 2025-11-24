<?php
include '../includes/db.php';
include '../includes/session.php';

$q = mysqli_query($conn, "SELECT b.*, l.nama_lapangan FROM booking b LEFT JOIN lapangan l ON b.lapangan_id = l.id ORDER BY b.created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Booking List - Admin</title>
    <link rel="stylesheet" href="assets_admin/style.css">
</head>

<body>
    <?php include 'nav_admin.php'; ?>
    <div class="admin-container">
        <h2>Semua Booking</h2>
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
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1;
                while ($r = mysqli_fetch_assoc($q)): ?>
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
                        <td><a class="btn-sm" href="booking_detail.php?id=<?= $r['id'] ?>">Detail</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>