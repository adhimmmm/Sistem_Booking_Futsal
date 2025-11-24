<?php
include '../includes/db.php';
include '../includes/session.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: booking_list.php");
    exit;
}

$res = mysqli_query($conn, "SELECT b.*, l.nama_lapangan, l.harga_per_jam FROM booking b LEFT JOIN lapangan l ON b.lapangan_id = l.id WHERE b.id = $id");
$booking = mysqli_fetch_assoc($res);
if (!$booking) {
    echo "Booking tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'validate') {
        $note = mysqli_real_escape_string($conn, $_POST['note'] ?? '');
        mysqli_query($conn, "UPDATE booking SET status='valid', admin_note = '" . $note . "' WHERE id = $id");
        $lj = "INSERT INTO jadwal_terblokir (lapangan_id, tanggal, jam_mulai, jam_selesai, booking_id) VALUES (" . intval($booking['lapangan_id']) . ", '" . mysqli_real_escape_string($conn, $booking['tanggal_booking']) . "', '" . mysqli_real_escape_string($conn, $booking['jam_mulai']) . "', '" . mysqli_real_escape_string($conn, $booking['jam_selesai']) . "', $id)";
        mysqli_query($conn, $lj);
        header("Location: booking_detail.php?id=$id&ok=validated");
        exit;
    } elseif (isset($_POST['action']) && $_POST['action'] === 'reject') {
        $note = mysqli_real_escape_string($conn, $_POST['note'] ?? '');
        mysqli_query($conn, "UPDATE booking SET status='ditolak', admin_note='" . $note . "' WHERE id=$id");
        header("Location: booking_detail.php?id=$id&ok=rejected");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Detail Booking - Admin</title>
    <link rel="stylesheet" href="assets_admin/style.css">
</head>

<body>
    <?php include 'nav_admin.php'; ?>
    <div class="admin-container">
        <h2>Detail Booking #<?= intval($booking['id']) ?></h2>
        <div class="detail-grid">
            <div>
                <p><strong>Nama:</strong> <?= htmlspecialchars($booking['nama_pemesan']) ?></p>
                <p><strong>WA:</strong> <?= htmlspecialchars($booking['nomor_wa']) ?></p>
                <p><strong>Lapangan:</strong> <?= htmlspecialchars($booking['nama_lapangan']) ?></p>
                <p><strong>Tanggal:</strong> <?= htmlspecialchars($booking['tanggal_booking']) ?></p>
                <p><strong>Jam:</strong> <?= htmlspecialchars($booking['jam_mulai'] . ' - ' . $booking['jam_selesai']) ?></p>
                <p><strong>Total:</strong> Rp <?= number_format($booking['total_harga']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($booking['status']) ?></p>
                <p><strong>Catatan Admin:</strong> <?= htmlspecialchars($booking['admin_note'] ?? '-') ?></p>
                <p><strong>Dibuat:</strong> <?= htmlspecialchars($booking['created_at']) ?></p>
                <p>
                    <a class="btn-sm" href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $booking['nomor_wa']) ?>?text=Halo%20<?= rawurlencode($booking['nama_pemesan']) ?>%2C%20ini%20admin%20lapangan.%20Terkait%20booking%20Anda%20pada%20<?= rawurlencode($booking['tanggal_booking']) ?>%20jam%20<?= rawurlencode($booking['jam_mulai']) ?>." target="_blank">Hubungi via WA</a>
                </p>
            </div>
            <div>
                <p><strong>Bukti Pembayaran:</strong></p>
                <?php if (!empty($booking['bukti_pembayaran']) && file_exists(__DIR__ . '/../' . $booking['bukti_pembayaran'])): ?>
                    <a href="../<?= htmlspecialchars($booking['bukti_pembayaran']) ?>" target="_blank">
                        <img src="../<?= htmlspecialchars($booking['bukti_pembayaran']) ?>" style="max-width:100%; border-radius:6px; box-shadow: 0 6px 20px rgba(0,0,0,0.5);">
                    </a>
                <?php else: ?>
                    <div class="no-file">Tidak ada bukti terupload</div>
                <?php endif; ?>
            </div>
        </div>

        <hr style="margin:18px 0; border-color: rgba(255,255,255,0.04);">

        <form method="POST" action="">
            <label>Catatan untuk pemesan (opsional)
                <textarea name="note" rows="3" style="width:100%; padding:8px; border-radius:6px;"></textarea>
            </label>

            <div style="display:flex; gap:10px; margin-top:12px;">
                <?php if ($booking['status'] !== 'valid'): ?>
                    <button name="action" value="validate" class="btn">Validasi (Setujui)</button>
                <?php endif; ?>
                <?php if ($booking['status'] !== 'ditolak'): ?>
                    <button name="action" value="reject" class="btn btn-danger">Tolak</button>
                <?php endif; ?>
            </div>
        </form>

    </div>
</body>

</html>