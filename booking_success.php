<?php
// booking_success.php
include 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Booking Berhasil</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .success-card {
            max-width: 600px;
            margin: 80px auto;
            background: #071022;
            padding: 28px;
            border-radius: 12px;
            text-align: center;
            color: #dff2ff;
        }

        .success-card h2 {
            color: #a7ffcb
        }

        .success-card a {
            display: inline-block;
            margin-top: 12px;
            color: #fff;
            background: #0066ff;
            padding: 10px 14px;
            border-radius: 8px;
            text-decoration: none
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="success-card">
            <h2>Booking Berhasil!</h2>
            <p>Terima kasih — booking Anda telah kami terima dan menunggu verifikasi admin.</p>
            <p>Admin akan menghubungi melalui WhatsApp setelah bukti pembayaran diverifikasi.</p>
            <a href="index.php">Kembali ke Beranda</a>
        </div>
    </div>
</body>

</html>