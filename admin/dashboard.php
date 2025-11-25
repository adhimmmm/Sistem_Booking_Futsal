<?php
// admin/dashboard.php (Enhanced with Search & Better Modal)
session_start();
include '../includes/db.php';
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: login.php");
    exit;
}

// Handle actions (validate/pending/delete)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] === 'valid') {
        mysqli_query($conn, "UPDATE booking SET status='valid' WHERE id = $id");
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

// Filters
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = [];
if (in_array($statusFilter, ['pending', 'valid', 'ditolak'])) {
    $where[] = "b.status = '" . mysqli_real_escape_string($conn, $statusFilter) . "'";
}
if (!empty($searchQuery)) {
    $safe_search = mysqli_real_escape_string($conn, $searchQuery);
    $where[] = "(b.nama_pemesan LIKE '%$safe_search%' OR b.nomor_wa LIKE '%$safe_search%' OR l.nama_lapangan LIKE '%$safe_search%')";
}

$whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$tot_pending = intval(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM booking WHERE status='pending'"))['c']);
$tot_valid   = intval(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM booking WHERE status='valid'"))['c']);
$tot_all     = intval(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM booking"))['c']);

$sql = "SELECT b.*, l.nama_lapangan FROM booking b LEFT JOIN lapangan l ON b.lapangan_id = l.id $whereClause ORDER BY b.created_at DESC";
$res = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Admin Dashboard - Futsal Booking</title>
    <link rel="stylesheet" href="css_admin/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="admin-top">
        <div class="container-nav">
            <a href="dashboard.php" class="brand">ADMIN<span style="color:#64748B; font-weight:400;">PANEL</span></a>
            <div class="actions">
                <a href="../index.php">Lihat Website</a>
                <a href="lapangan_list.php">Kelola Lapangan</a>
                <a href="logout.php" class="btn-danger">Logout</a>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <h1>Dashboard Booking</h1>

        <?php if (isset($_GET['ok'])): ?>
            <?php if ($_GET['ok'] == 'valid'): ?>
                <div class="alert alert-success">✅ Booking berhasil divalidasi.</div>
            <?php elseif ($_GET['ok'] == 'delete'): ?>
                <div class="alert">🗑️ Booking berhasil dihapus.</div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="stats">
            <div class="stat">
                <div class="stat-label">⏳ Pending</div>
                <strong><?= $tot_pending ?></strong>
            </div>
            <div class="stat">
                <div class="stat-label">✅ Validated</div>
                <strong><?= $tot_valid ?></strong>
            </div>
            <div class="stat">
                <div class="stat-label">📊 Total Booking</div>
                <strong><?= $tot_all ?></strong>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="search-filter-bar">
            <form method="GET" action="">
                <div class="search-row">
                    <div class="search-input-wrapper">
                        <input type="text" name="search" placeholder="Cari nama pemesan, nomor WA, atau lapangan..." value="<?= htmlspecialchars($searchQuery) ?>">
                    </div>
                    <button type="submit" class="btn">Cari</button>
                    <?php if (!empty($searchQuery)): ?>
                        <a href="dashboard.php" class="btn" style="background:var(--gray-500)">Reset</a>
                    <?php endif; ?>
                </div>
            </form>

            <div style="margin-top:16px;">
                <div class="filter-tabs">
                    <a href="dashboard.php?status=all<?= !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : '' ?>"
                        class="filter-tab <?= $statusFilter === 'all' ? 'active' : '' ?>">
                        Semua (<?= $tot_all ?>)
                    </a>
                    <a href="dashboard.php?status=pending<?= !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : '' ?>"
                        class="filter-tab <?= $statusFilter === 'pending' ? 'active' : '' ?>">
                        Pending (<?= $tot_pending ?>)
                    </a>
                    <a href="dashboard.php?status=valid<?= !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : '' ?>"
                        class="filter-tab <?= $statusFilter === 'valid' ? 'active' : '' ?>">
                        Valid (<?= $tot_valid ?>)
                    </a>
                    <a href="dashboard.php?status=ditolak<?= !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : '' ?>"
                        class="filter-tab <?= $statusFilter === 'ditolak' ? 'active' : '' ?>">
                        Ditolak
                    </a>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Pemesan</th>
                        <th>WhatsApp</th>
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
                    <?php
                    $i = 1;
                    $no_data = true;
                    while ($r = mysqli_fetch_assoc($res)):
                        $no_data = false;
                    ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><strong><?= htmlspecialchars($r['nama_pemesan']) ?></strong></td>
                            <td><?= htmlspecialchars($r['nomor_wa']) ?></td>
                            <td><?= htmlspecialchars($r['nama_lapangan']) ?></td>
                            <td><?= date('d M Y', strtotime($r['tanggal_booking'])) ?></td>
                            <td><?= htmlspecialchars($r['jam_mulai'] . ' - ' . $r['jam_selesai']) ?></td>
                            <td><strong>Rp <?= number_format($r['total_harga'], 0, ',', '.') ?></strong></td>
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
                                    <button class="btn-sm" style="background:var(--accent);color:white;border:none;cursor:pointer;"
                                        onclick='openDetailModal(<?= json_encode($r) ?>)'>
                                        📄 Lihat Detail
                                    </button>
                                <?php else: ?>
                                    <span style="color:var(--gray-500)">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-group">
                                    <?php if ($r['status'] !== 'valid'): ?>
                                        <a class="btn-sm btn-success" href="?action=valid&id=<?= $r['id'] ?>"
                                            onclick="return confirm('Validasi booking ini?')">✅ Valid</a>
                                    <?php endif; ?>

                                    <a class="btn-sm btn-wa" target="_blank"
                                        href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $r['nomor_wa']) ?>?text=Halo%20<?= rawurlencode($r['nama_pemesan']) ?>%2C%20mengenai%20booking%20anda%20pada%20<?= rawurlencode($r['tanggal_booking']) ?>">
                                        💬 WA
                                    </a>

                                    <a class="btn-sm btn-danger" href="?action=delete&id=<?= $r['id'] ?>"
                                        onclick="return confirm('Hapus booking ini?')">🗑️ Hapus</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                    <?php if ($no_data): ?>
                        <tr>
                            <td colspan="10" style="text-align:center;padding:40px;color:var(--gray-500);">
                                <?php if (!empty($searchQuery)): ?>
                                    Tidak ada hasil untuk pencarian "<?= htmlspecialchars($searchQuery) ?>"
                                <?php else: ?>
                                    Belum ada booking yang masuk
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Enhanced Modal for Booking Detail -->
    <div id="detailModal" class="modal" onclick="closeDetailModal()">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3>📋 Detail Booking</h3>
                <button class="modal-close" onclick="closeDetailModal()">✕ Tutup</button>
            </div>
            <div class="modal-body">
                <div class="modal-info">
                    <div class="modal-info-item">
                        <div class="modal-info-label">Nama Pemesan</div>
                        <div class="modal-info-value" id="modal-nama">-</div>
                    </div>
                    <div class="modal-info-item">
                        <div class="modal-info-label">Nomor WhatsApp</div>
                        <div class="modal-info-value" id="modal-wa">-</div>
                    </div>
                    <div class="modal-info-item">
                        <div class="modal-info-label">Lapangan</div>
                        <div class="modal-info-value" id="modal-lapangan">-</div>
                    </div>
                    <div class="modal-info-item">
                        <div class="modal-info-label">Tanggal Booking</div>
                        <div class="modal-info-value" id="modal-tanggal">-</div>
                    </div>
                    <div class="modal-info-item">
                        <div class="modal-info-label">Jam Main</div>
                        <div class="modal-info-value" id="modal-jam">-</div>
                    </div>
                    <div class="modal-info-item">
                        <div class="modal-info-label">Total Pembayaran</div>
                        <div class="modal-info-value" id="modal-total" style="font-size:20px;color:var(--primary);font-weight:700;">-</div>
                    </div>
                    <div class="modal-info-item">
                        <div class="modal-info-label">Status</div>
                        <div class="modal-info-value" id="modal-status">-</div>
                    </div>
                    <div class="modal-info-item">
                        <div class="modal-info-label">Waktu Booking</div>
                        <div class="modal-info-value" id="modal-created">-</div>
                    </div>
                </div>
                <div class="modal-image">
                    <div class="modal-info-label" style="margin-bottom:12px;">Bukti Pembayaran</div>
                    <img id="modal-img" src="" alt="Bukti Pembayaran" style="max-width:100%;border-radius:8px;">
                </div>
            </div>
        </div>
    </div>

    <script>
        function openDetailModal(data) {
            // Populate data
            document.getElementById('modal-nama').textContent = data.nama_pemesan;
            document.getElementById('modal-wa').textContent = data.nomor_wa;
            document.getElementById('modal-lapangan').textContent = data.nama_lapangan;
            document.getElementById('modal-tanggal').textContent = formatDate(data.tanggal_booking);
            document.getElementById('modal-jam').textContent = data.jam_mulai + ' - ' + data.jam_selesai;
            document.getElementById('modal-total').textContent = 'Rp ' + parseInt(data.total_harga).toLocaleString('id-ID');
            document.getElementById('modal-created').textContent = data.created_at;

            // Status badge
            let statusBadge = '';
            if (data.status === 'pending') {
                statusBadge = '<span class="badge badge-pending">Pending</span>';
            } else if (data.status === 'valid') {
                statusBadge = '<span class="badge badge-valid">Valid</span>';
            } else {
                statusBadge = '<span class="badge badge-reject">' + data.status + '</span>';
            }
            document.getElementById('modal-status').innerHTML = statusBadge;

            // Image
            if (data.bukti_pembayaran) {
                document.getElementById('modal-img').src = '../' + data.bukti_pembayaran;
            }

            // Show modal
            document.getElementById('detailModal').style.display = 'flex';
        }

        function closeDetailModal() {
            document.getElementById('detailModal').style.display = 'none';
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            const options = {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            };
            return date.toLocaleDateString('id-ID', options);
        }

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDetailModal();
            }
        });
    </script>

</body>

</html>