<?php
include '../includes/db.php';
include '../includes/session.php';

$msg = '';
$msg_type = 'danger';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lapangan']);
    $des  = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga = intval($_POST['harga_per_jam']);
    $status = in_array($_POST['status'], ['aktif', 'nonaktif']) ? $_POST['status'] : 'aktif';

    $foto_path = '';

    // Handle file upload
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/jpg', 'image/png'];
        $file_type = $_FILES['foto']['type'];
        $file_size = $_FILES['foto']['size'];

        if (!in_array($file_type, $allowed)) {
            $msg = "❌ Format file tidak didukung. Gunakan JPG, JPEG, atau PNG.";
        } elseif ($file_size > 5 * 1024 * 1024) { // 5MB
            $msg = "❌ Ukuran file terlalu besar. Maksimal 5MB.";
        } else {
            $upload_dir = __DIR__ . '/../uploads/lapangan/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $new_filename = 'lapangan_' . time() . '_' . uniqid() . '.' . $ext;
            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $destination)) {
                $foto_path = 'uploads/lapangan/' . $new_filename;
            } else {
                $msg = "❌ Gagal mengupload foto.";
            }
        }
    }

    if (empty($msg)) {
        $sql = "INSERT INTO lapangan (nama_lapangan, deskripsi, harga_per_jam, status, foto, created_at) 
                VALUES ('$nama', '$des', $harga, '$status', '$foto_path', NOW())";

        if (mysqli_query($conn, $sql)) {
            header("Location: lapangan_list.php?ok=tambah");
            exit;
        } else {
            $msg = "❌ Gagal menyimpan: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Tambah Lapangan - Admin</title>
    <link rel="stylesheet" href="css_admin/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="admin-top">
        <div class="container-nav">
            <a href="dashboard.php" class="brand">ADMIN<span style="color:#64748B; font-weight:400;">PANEL</span></a>
            <div class="actions">
                <a href="dashboard.php">Dashboard</a>
                <a href="lapangan_list.php">Kelola Lapangan</a>
                <a href="logout.php" class="btn-danger">Logout</a>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <h1>➕ Tambah Lapangan Baru</h1>

        <?php if ($msg): ?>
            <div class="alert <?= $msg_type === 'success' ? 'alert-success' : '' ?>"><?= $msg ?></div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST" enctype="multipart/form-data">
                <label>
                    <span>Nama Lapangan <span style="color:var(--danger)">*</span></span>
                    <input type="text" name="nama_lapangan" required placeholder="Contoh: Lapangan A">
                </label>

                <label>
                    <span>Deskripsi</span>
                    <textarea name="deskripsi" rows="4" placeholder="Jelaskan fasilitas dan keunggulan lapangan ini..."></textarea>
                </label>

                <label>
                    <span>Harga per Jam (Rp) <span style="color:var(--danger)">*</span></span>
                    <input type="number" name="harga_per_jam" required placeholder="150000" min="0">
                </label>

                <label>
                    <span>Foto Lapangan</span>
                    <input type="file" name="foto" accept="image/jpeg,image/jpg,image/png" id="fotoInput">
                    <small style="color:var(--gray-500);display:block;margin-top:8px;">
                        Format: JPG, JPEG, PNG | Maksimal: 5MB | Rekomendasi ukuran: 800x600px
                    </small>
                </label>

                <!-- Image Preview -->
                <div id="imagePreview" style="display:none;margin-top:16px;padding:16px;background:var(--gray-100);border-radius:var(--radius);border:1px solid var(--border);">
                    <div style="font-size:14px;font-weight:600;margin-bottom:12px;color:var(--gray-700);">Preview Foto:</div>
                    <img id="previewImg" src="" alt="Preview" style="max-width:100%;max-height:300px;border-radius:var(--radius);box-shadow:var(--shadow-md);">
                </div>

                <label>
                    <span>Status <span style="color:var(--danger)">*</span></span>
                    <select name="status">
                        <option value="aktif">Aktif (Tersedia untuk booking)</option>
                        <option value="nonaktif">Nonaktif (Tidak tersedia)</option>
                    </select>
                </label>

                <div style="display:flex;gap:12px;margin-top:24px;">
                    <button class="btn" type="submit">💾 Simpan Lapangan</button>
                    <a href="lapangan_list.php" class="btn" style="background:var(--gray-500);">← Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Image preview
        document.getElementById('fotoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                document.getElementById('imagePreview').style.display = 'none';
            }
        });
    </script>

</body>

</html>