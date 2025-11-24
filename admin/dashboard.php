<?php
session_start();
include '../includes/db.php';

// Cek login
if (!isset($_SESSION['admin_logged'])) {
    header("Location: login.php");
    exit;
}

// Aksi CRUD status booking
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] == "valid") {
        mysqli_query($conn, "UPDATE booking SET status='valid' WHERE id=$id");
    } elseif ($_GET['action'] == "pending") {
        mysqli_query($conn, "UPDATE booking SET status='pending' WHERE id=$id");
    } elseif ($_GET['action'] == "delete") {
        mysqli_query($conn, "DELETE FROM booking WHERE id=$id");
    }
    header("Location: dashboard.php");
    exit;
}

// Statistik
$tot_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM booking WHERE status='pending'"))['c'];
$tot_valid   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM booking WHERE status='valid'"))['c'];
$tot_all     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM booking"))['c'];

// Booking terbaru
$res = mysqli_query($conn, "
    SELECT b.*, l.nama_lapangan 
    FROM booking b 
    LEFT JOIN lapangan l ON b.lapangan_id = l.id 
    ORDER BY b.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Futsal Booking</title>
    <style>
        body {
            margin: 0;
            background: #f4f6f9;
            font-family: Arial, sans-serif;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 230px;
            background: #111827;
            color: white;
            height: 100vh;
            padding: 20px;
            position: fixed;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            padding: 12px;
            text-decoration: none;
            color: white;
            margin-bottom: 8px;
            border-radius: 6px;
        }

        .sidebar a:hover {
            background: #1f2937;
        }

        /* main content */
        .main {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .logout-btn {
            background: #dc2626;
            padding: 8px 15px;
            color: white;
            border-radius: 6px;
            text-decoration: none;
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 200px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card h3 {
            margin: 0;
        }

        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f0f0f0;
        }

        .btn {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            margin-right: 4px;
            font-size: 13px;
        }

        .btn-valid {
            background: #16a34a;
        }

        .btn-pending {
            background: #ca8a04;
        }

        .btn-del {
            background: #dc2626;
        }

        .btn-wa {
            background: #0ea5e9;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 90%;
            text-align: center;
        }

        .modal-content img {
            max-width: 500px;
            border-radius: 8px;
        }

        .close {
            background: red;
            color: white;
            padding: 6px 12px;
            margin-top: 10px;
            cursor: pointer;
            border: none;
            border-radius: 6px;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="../index.php">Lihat Website</a>
        <a href="logout.php" class="logout-btn" style="margin-top: 30px;">Logout</a>
    </div>

    <div class="main">

        <div class="header">
            <h1>Dashboard Admin</h1>
        </div>

        <div class="stats">
            <div class="card">
                <h3>Pending</h3>
                <p><?= $tot_pending ?></p>
            </div>
            <div class="card">
                <h3>Valid</h3>
                <p><?= $tot_valid ?></p>
            </div>
            <div class="card">
                <h3>Total Booking</h3>
                <p><?= $tot_all ?></p>
            </div>
        </div>

        <h2>Data Booking</h2>
        <table>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Lapangan</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Status</th>
                <th>Bukti</th>
                <th>Aksi</th>
            </tr>

            <?php $i = 1;
            while ($r = mysqli_fetch_assoc($res)): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= $r['nama_pemesan'] ?></td>
                    <td><?= $r['nama_lapangan'] ?></td>
                    <td><?= $r['tanggal_booking'] ?></td>
                    <td><?= $r['jam_mulai'] ?> - <?= $r['jam_selesai'] ?></td>
                    <td><?= $r['status'] ?></td>

                    <!-- Bukti popup -->
                    <td>
                        <?php if ($r['bukti_transfer']): ?>
                            <button onclick="openModal('uploads/<?= $r['bukti_transfer'] ?>')">Lihat</button>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>

                    <td>
                        <a class="btn btn-valid" href="?action=valid&id=<?= $r['id'] ?>">Valid</a>
                        <a class="btn btn-pending" href="?action=pending&id=<?= $r['id'] ?>">Pending</a>
                        <a class="btn btn-del" onclick="return confirm('Hapus booking?')" href="?action=delete&id=<?= $r['id'] ?>">Delete</a>

                        <!-- WhatsApp Manual -->
                        <a class="btn btn-wa" target="_blank" href="https://wa.me/<?= $r['no_hp'] ?>">WA</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

    </div>

    <!-- Modal Pop-up -->
    <div id="modal" class="modal" onclick="closeModal()">
        <div class="modal-content" onclick="event.stopPropagation()">
            <img id="modal-img">
            <button class="close" onclick="closeModal()">Tutup</button>
        </div>
    </div>

    <script>
        function openModal(src) {
            document.getElementById("modal-img").src = src;
            document.getElementById("modal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("modal").style.display = "none";
        }
    </script>

</body>

</html>