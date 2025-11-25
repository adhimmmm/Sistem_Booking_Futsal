<?php
include '../includes/db.php';
include '../includes/session.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: lapangan_list.php");
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lapangan']);
    $des  = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga = intval($_POST['harga_per_jam']);
    $status = in_array($_POST['status'], ['aktif', 'nonaktif']) ? $_POST['status'] : 'aktif';

    // Get current photo
    $current = mysqli_fetch_assoc(mysqli_query($conn, "SELECT foto FROM lapangan WHERE id = $id"));
    $foto_path = $current['foto'];

    // Handle file upload
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/jpg', 'image/png'];
        $file_type = $_FILES['foto']['type'];
        $file_size = $_FILES['foto']['size'];

        if (!in_array($file_type, $allowed)) {
            $msg = "❌ Format file tidak didukung. Gunakan JPG, JPEG, atau PNG.";
        } elseif ($file_size > 5 * 1024 * 1024) {
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
                // Delete old photo
                if (!empty($current['foto']) && file_exists(__DIR__ . '/../' . $current['foto'])) {
                    @unlink(__DIR__ . '/../' . $current['foto']);
                }
                $foto_path = 'uploads/lapangan/' . $new_filename;
            } else {
                $msg = "❌ Gagal mengupload foto.";
            }
        }
    }

    // Handle delete photo
    if (isset($_POST['delete_foto']) && $_POST['delete_foto'] === '1') {
        if (!empty($current['foto']) && file_exists(__DIR__ . '/../' . $current['foto'])) {
            @unlink(__DIR__ . '/../' . $current['foto']);
        }
        $foto_path = '';
    }

    if (empty($msg) || !isset($_FILES['foto'])) {
        $sql = "UPDATE lapangan SET 
                nama_lapangan='$nama', 
                deskripsi='$des', 
                harga_per_jam=$harga, 
                status='$status',
                foto='$foto_path'
                WHERE id=$id";

        if (mysqli_query($conn, $sql)) {
            header("Location: lapangan_edit.php?id=$id&ok=1");
            exit;
        } else {
            $msg = "❌ Gagal: " . mysqli_error($conn);
        }
    }
}

$res = mysqli_query($conn, "SELECT * FROM lapangan WHERE id = $id");
$row = mysqli_fetch_assoc($res);
if (!$row) {
    echo "Lapangan tidak ditemukan";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Edit Lapangan - Admin</title>
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
        <h1>✏️ Edit Lapangan: <?= htmlspecialchars($row['nama_lapangan']) ?></h1>

        <?php if ($msg): ?>
            <div class="alert"><?= $msg ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['ok']) && $_GET['ok'] == '1'): ?>
            <div class="alert alert-success">✅ Lapangan berhasil diupdate.</div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST" enctype="multipart/form-data" id="editForm">
                <label>
                    <span>Nama Lapangan <span style="color:var(--danger)">*</span></span>
                    <input type="text" name="nama_lapangan" required value="<?= htmlspecialchars($row['nama_lapangan']) ?>">
                </label>

                <label>
                    <span>Deskripsi</span>
                    <textarea name="deskripsi" rows="4"><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                </label>

                <label>
                    <span>Harga per Jam (Rp) <span style="color:var(--danger)">*</span></span>
                    <input type="number" name="harga_per_jam" required value="<?= htmlspecialchars($row['harga_per_jam']) ?>" min="0">
                </label>

                <!-- Current Photo -->
                <?php if (!empty($row['foto']) && file_exists(__DIR__ . '/../' . $row['foto'])): ?>
                    <div class="image-preview-box">
                        <h4>📷 Foto Saat Ini:</h4>
                        <img src="../<?= htmlspecialchars($row['foto']) ?>"
                            alt="<?= htmlspecialchars($row['nama_lapangan']) ?>"
                            class="current-image">
                        <div>
                            <label style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;">
                                <input type="checkbox" name="delete_foto" value="1" id="deleteFotoCheck">
                                <span style="color:var(--danger);font-weight:500;">🗑️ Hapus foto ini</span>
                            </label>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="padding:20px;background:var(--gray-100);border-radius:var(--radius);text-align:center;color:var(--gray-500);margin-bottom:16px;">
                        📷 Belum ada foto untuk lapangan ini
                    </div>
                <?php endif; ?>

                <label>
                    <span>Upload Foto Baru (Opsional)</span>
                    <input type="file" name="foto" accept="image/jpeg,image/jpg,image/png" id="fotoInput">
                    <small style="color:var(--gray-500);display:block;margin-top:8px;">
                        Format: JPG, JPEG, PNG | Maksimal: 5MB | Rekomendasi ukuran: 800x600px<br>
                        <?php if (!empty($row['foto'])): ?>
                            <strong>Catatan:</strong> Upload foto baru akan mengganti foto yang ada sekarang.
                        <?php endif; ?>
                    </small>
                </label>

                <!-- New Image Preview -->
                <div id="imagePreview" style="display:none;margin-top:16px;padding:16px;background:var(--primary-light);border-radius:var(--radius);border:2px solid var(--primary);">
                    <div style="font-size:14px;font-weight:600;margin-bottom:12px;color:var(--primary);">Preview Foto Baru:</div>
                    <img id="previewImg" src="" alt="Preview" style="max-width:100%;max-height:300px;border-radius:var(--radius);box-shadow:var(--shadow-md);">
                </div>

                <label>
                    <span>Status <span style="color:var(--danger)">*</span></span>
                    <select name="status">
                        <option value="aktif" <?= $row['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= $row['status'] == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </label>

                <div style="display:flex;gap:12px;margin-top:24px;">
                    <button class="btn" type="submit">💾 Update Lapangan</button>
                    <a href="lapangan_list.php" class="btn" style="background:var(--gray-500);">← Kembali</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Image preview for new upload
        document.getElementById('fotoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                }
                reader.readAsDataURL(file);

                // Uncheck delete foto if new photo is selected
                document.getElementById('deleteFotoCheck')?.checked = false;
            } else {
                document.getElementById('imagePreview').style.display = 'none';
            }
        });

        // Clear file input when delete is checked
        const deleteCheck = document.getElementById('deleteFotoCheck');
        if (deleteCheck) {
            deleteCheck.addEventListener('change', function() {
                if (this.checked) {
                    document.getElementById('fotoInput').value = '';
                    document.getElementById('imagePreview').style.display = 'none';
                }
            });
        }
    </script>

</body>

</html>