<?php include 'includes/db.php'; ?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Booking Lapangan Futsal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #00c3ff, #0044ff);
            min-height: 100vh;
        }

        /* NAVBAR */
        .navbar {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            color: white;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 600;
        }

        /* HERO */
        .hero {
            text-align: center;
            color: white;
            margin-top: 50px;
            padding: 20px;
        }

        .hero h2 {
            font-size: 42px;
            line-height: 1.2;
            margin-bottom: 10px;
        }

        .hero p {
            font-size: 18px;
            opacity: 0.9;
        }

        /* CONTAINER FORM */
        .booking-container {
            max-width: 550px;
            margin: 40px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        label {
            font-weight: 600;
            margin-top: 12px;
            display: block;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            border-radius: 8px;
            border: 1.5px solid #ccc;
            font-size: 15px;
            transition: 0.2s;
        }

        input:focus,
        select:focus {
            border-color: #0066ff;
            outline: none;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #0066ff;
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 17px;
            margin-top: 18px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.2s;
        }

        button:hover {
            background: #0049c7;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            color: white;
            font-size: 14px;
            opacity: 0.8;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <div class="navbar">
        <h1>Futsal Booking</h1>
    </div>

    <!-- HERO -->
    <div class="hero">
        <h2>Booking Lapangan Futsal<br>Mudah & Cepat</h2>
        <p>Pilih lapangan, pilih jadwal, upload bukti transfer, selesai!</p>
    </div>

    <!-- FORM BOOKING -->
    <div class="booking-container">

        <h3 style="text-align:center; margin-bottom:20px;">Form Booking</h3>

        <form action="booking_submit.php" method="POST" enctype="multipart/form-data">

            <label>Nama Pemesan</label>
            <input type="text" name="nama_pemesan" placeholder="Nama lengkap" required>

            <label>Nomor WhatsApp</label>
            <input type="text" name="nomor_wa" placeholder="Contoh: 6281234567890" required>

            <label>Pilih Lapangan</label>
            <select name="lapangan_id" required>
                <option value="">-- Pilih Lapangan --</option>
                <?php
                $lap = mysqli_query($conn, "SELECT * FROM lapangan WHERE status='aktif'");
                while ($l = mysqli_fetch_assoc($lap)) {
                    echo "<option value='" . $l['id'] . "'>" . $l['nama_lapangan'] . " - Rp " . number_format($l['harga_per_jam']) . "/jam</option>";
                }
                ?>
            </select>

            <label>Tanggal Booking</label>
            <input type="date" name="tanggal_booking" required>

            <label>Jam Mulai</label>
            <input type="time" name="jam_mulai" required>

            <label>Jam Selesai</label>
            <input type="time" name="jam_selesai" required>

            <label>Upload Bukti Pembayaran (Opsional)</label>
            <input type="file" name="bukti_pembayaran" accept="image/*">

            <button type="submit">Kirim Booking</button>
        </form>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        &copy; <?= date('Y') ?> Sistem Booking Lapangan Futsal
    </div>

</body>

</html>