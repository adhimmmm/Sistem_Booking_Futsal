<?php
// booking_submit.php
include 'includes/db.php';

function s($v)
{
    return htmlspecialchars(trim($v));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$nama = s($_POST['nama_pemesan'] ?? '');
$wa   = s($_POST['nomor_wa'] ?? '');
$lapangan_id = intval($_POST['lapangan_id'] ?? 0);
$tanggal = $_POST['tanggal_booking'] ?? '';
$jam_mulai = $_POST['jam_mulai'] ?? '';
$jam_selesai = $_POST['jam_selesai'] ?? '';

$errors = [];
if ($nama === '') $errors[] = "Nama wajib diisi.";
if ($wa === '') $errors[] = "Nomor WhatsApp wajib diisi.";
if ($lapangan_id <= 0) $errors[] = "Pilih lapangan.";
if ($tanggal === '') $errors[] = "Tanggal booking wajib diisi.";
if ($jam_mulai === '' || $jam_selesai === '') $errors[] = "Jam mulai & jam selesai wajib diisi.";
if ($jam_mulai >= $jam_selesai) $errors[] = "Jam selesai harus lebih besar dari jam mulai.";

$bukti_path = null;
if (!empty($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] !== 4) {
    $file = $_FILES['bukti_pembayaran'];
    $allowed = ['image/jpeg', 'image/png', 'image/jpg'];
    if ($file['error'] !== 0) {
        $errors[] = "Upload bukti gagal.";
    } elseif (!in_array($file['type'], $allowed)) {
        $errors[] = "Format bukti harus JPG/PNG.";
    } elseif ($file['size'] > 5 * 1024 * 1024) {
        $errors[] = "Ukuran bukti maksimal 5MB.";
    } else {
        $folder = __DIR__ . '/uploads/bukti/';
        if (!is_dir($folder)) mkdir($folder, 0755, true);
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newname = 'bukti_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        $dest = $folder . $newname;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $bukti_path = 'uploads/bukti/' . $newname;
        } else {
            $errors[] = "Gagal menyimpan file bukti.";
        }
    }
}

if (empty($errors)) {
    $sql = "SELECT * FROM booking WHERE lapangan_id = ? AND tanggal_booking = ? AND status IN ('valid','pending')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $lapangan_id, $tanggal);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($r = mysqli_fetch_assoc($res)) {
        $existing_start = $r['jam_mulai'];
        $existing_end   = $r['jam_selesai'];
        if ($jam_mulai < $existing_end && $jam_selesai > $existing_start) {
            $errors[] = "Jadwal bentrok dengan booking lain (" . $r['nama_pemesan'] . " - " . $existing_start . "/" . $existing_end . ").";
            break;
        }
    }
    mysqli_stmt_close($stmt);
}

if (!empty($errors)) {
    if ($bukti_path && file_exists(__DIR__ . '/' . $bukti_path)) unlink(__DIR__ . '/' . $bukti_path);
    echo "<h3>Terjadi kesalahan:</h3><ul>";
    foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>";
    echo "</ul><a href='index.php'>Kembali</a>";
    exit;
}

$lap = mysqli_query($conn, "SELECT harga_per_jam FROM lapangan WHERE id = " . intval($lapangan_id));
$harga = 0;
if ($lap && $row = mysqli_fetch_assoc($lap)) {
    $harga = intval($row['harga_per_jam']);
}

$start = new DateTime($jam_mulai);
$end = new DateTime($jam_selesai);
$interval = $start->diff($end);
$hours = $interval->h + ($interval->i / 60.0);
$total = intval(round($hours * $harga));

$sql = "INSERT INTO booking (nama_pemesan, nomor_wa, lapangan_id, tanggal_booking, jam_mulai, jam_selesai, total_harga, bukti_pembayaran, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
$stmt = mysqli_prepare($conn, $sql);

// PERBAIKAN: Mengubah string format dari "sissssds" menjadi "ssisssis"
mysqli_stmt_bind_param($stmt, "ssisssis", $nama, $wa, $lapangan_id, $tanggal, $jam_mulai, $jam_selesai, $total, $bukti_path);

$ok = mysqli_stmt_execute($stmt);
if ($ok) {
    header("Location: booking_success.php");
    exit;
} else {
    if ($bukti_path && file_exists(__DIR__ . '/' . $bukti_path)) unlink(__DIR__ . '/' . $bukti_path);
    echo "Gagal menyimpan booking. Error: " . mysqli_error($conn);
    exit;
}