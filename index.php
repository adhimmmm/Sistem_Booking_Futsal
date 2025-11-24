<?php
// index.php
include 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Futsal Booking — Landing Page</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <!-- NAVBAR -->
    <header class="nav">
        <div class="container nav-inner">
            <div class="brand">
                <img src="/mnt/data/a728f4a7dbdeb486786ca67409f5d056.jpg" alt="logo" class="logo-small">
                <span>Futsal Booking</span>
            </div>
            <nav class="nav-links">
                <a href="#lapangan">Lapangan</a>
                <a href="#fitur">Fitur</a>
                <a href="#galeri">Galeri</a>
                <a href="#booking" class="btn-outline">Booking</a>
            </nav>
        </div>
    </header>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-bg" style="background-image: url('/mnt/data/a728f4a7dbdeb486786ca67409f5d056.jpg');"></div>
        <div class="hero-content container">
            <h1>Booking Lapangan Futsal<br><span>Cepat • Praktis • Aman</span></h1>
            <p>Pesan lapangan, upload bukti transfer, admin verifikasi. Tanpa ribet — tanpa login.</p>
            <div class="hero-cta">
                <a href="#fitur" class="btn-primary">Lihat Fitur</a>
                <a href="#booking" class="btn-secondary">Booking Sekarang</a>
            </div>
        </div>
    </section>

    <!-- FEATURE CARDS -->
    <section id="fitur" class="container section features">
        <div class="section-head">
            <h2>Kenapa Pilih Lapangan Kami?</h2>
            <p>Fasilitas lengkap, lapangan berkualitas, dan proses booking yang mudah.</p>
        </div>

        <div class="cards">
            <article class="card">
                <img src="https://images.unsplash.com/photo-1517927033932-b0ea1ff3b13b?auto=format&fit=crop&w=800&q=60" alt="jadwal">
                <div class="card-body">
                    <h3>Jadwal Real-time</h3>
                    <p>Cek ketersediaan jam secara langsung. Hindari double-booking.</p>
                </div>
            </article>

            <article class="card">
                <img src="https://images.unsplash.com/photo-1518105779142-d975f22f1b0f?auto=format&fit=crop&w=800&q=60" alt="harga">
                <div class="card-body">
                    <h3>Harga Transparan</h3>
                    <p>Tarif per jam yang jelas dan mudah dihitung otomatis di form booking.</p>
                </div>
            </article>

            <article class="card">
                <img src="https://images.unsplash.com/photo-1549880338-65ddcdfd017b?auto=format&fit=crop&w=800&q=60" alt="cara">
                <div class="card-body">
                    <h3>Cara Booking Mudah</h3>
                    <p>Isi formulir singkat, unggah bukti pembayaran, tunggu verifikasi admin.</p>
                </div>
            </article>

            <article class="card">
                <img src="https://images.unsplash.com/photo-1542831371-d531d36971e6?auto=format&fit=crop&w=800&q=60" alt="fasilitas">
                <div class="card-body">
                    <h3>Fasilitas Lengkap</h3>
                    <p>Ruang ganti, lampu malam, dan papan skor tersedia untuk kenyamananmu.</p>
                </div>
            </article>
        </div>
    </section>

    <!-- LAPANGAN (list from DB) -->
    <section id="lapangan" class="container section stadiums">
        <div class="section-head">
            <h2>Daftar Lapangan</h2>
            <p>Pilih lapangan sesuai kebutuhanmu. Harga per jam tertera.</p>
        </div>

        <div class="stadium-grid">
            <?php
            $q = mysqli_query($conn, "SELECT * FROM lapangan WHERE status='aktif'");
            while ($row = mysqli_fetch_assoc($q)) {
                $harga = number_format($row['harga_per_jam'], 0, ',', '.');
                echo '
            <div class="stadium-card">
                <div class="stadium-thumb" style="background-image:url(\'https://images.unsplash.com/photo-1526403224740-9b9b9f5b6a77?auto=format&fit=crop&w=800&q=60\')"></div>
                <div class="stadium-body">
                    <h3>' . htmlspecialchars($row['nama_lapangan']) . '</h3>
                    <p class="muted">Rp ' . $harga . '/jam</p>
                    <p class="small">' . htmlspecialchars(substr($row['deskripsi'], 0, 120)) . '</p>
                    <a class="btn-link" href="#booking">Booking</a>
                </div>
            </div>';
            }
            ?>
        </div>
    </section>

    <!-- PROMO / CTA -->
    <section class="promo">
        <div class="container promo-inner">
            <div>
                <h2>Ayo Booking Sekarang</h2>
                <p>Dapatkan lapangan terbaik untuk timmu. Mudah, cepat, tanpa harus registrasi.</p>
            </div>
            <div>
                <a href="#booking" class="btn-primary">Booking Sekarang</a>
            </div>
        </div>
    </section>

    <!-- GALLERY -->
    <section id="galeri" class="container section gallery">
        <div class="section-head">
            <h2>Galeri</h2>
            <p>Beberapa momen dan kondisi lapangan.</p>
        </div>
        <div class="gallery-grid">
            <img src="https://images.unsplash.com/photo-1508609349937-5ec4ae374ebf?auto=format&fit=crop&w=800&q=60" alt="">
            <img src="https://images.unsplash.com/photo-1524492412937-4961d3ffb6c1?auto=format&fit=crop&w=800&q=60" alt="">
            <img src="https://images.unsplash.com/photo-1517927033932-b0ea1ff3b13b?auto=format&fit=crop&w=800&q=60" alt="">
        </div>
    </section>

    <!-- SPONSOR -->
    <section class="container section sponsor">
        <h3>Our Sponsors</h3>
        <div class="sponsor-logos">
            <img src="https://upload.wikimedia.org/wikipedia/commons/2/20/Coca-Cola_logo.svg" alt="coca">
            <img src="https://upload.wikimedia.org/wikipedia/commons/a/a6/Logo_NIKE.svg" alt="nike">
            <img src="https://upload.wikimedia.org/wikipedia/commons/3/3e/Ford_logo_flat.svg" alt="ford">
        </div>
    </section>

    <!-- BOOKING FORM (Bottom) -->
    <section id="booking" class="container section booking">
        <div class="booking-wrap">
            <div class="booking-info">
                <h2>Form Booking</h2>
                <p>Isi data berikut untuk melakukan pemesanan. Admin akan menghubungi via WhatsApp setelah verifikasi bukti transfer.</p>
                <ul class="info-list">
                    <li><strong>Tanpa Login:</strong> Cukup isi form</li>
                    <li><strong>Upload Bukti:</strong> JPG/PNG maks 5MB</li>
                    <li><strong>Verifikasi:</strong> Manual oleh admin</li>
                </ul>
            </div>

            <div class="booking-form-card">
                <form action="booking_submit.php" method="POST" enctype="multipart/form-data" class="form-grid">
                    <label>Nama Pemesan
                        <input type="text" name="nama_pemesan" required>
                    </label>

                    <label>Nomor WhatsApp
                        <input type="text" name="nomor_wa" placeholder="6281234567890" required>
                    </label>

                    <label>Pilih Lapangan
                        <select name="lapangan_id" required>
                            <option value="">-- Pilih Lapangan --</option>
                            <?php
                            $lap = mysqli_query($conn, "SELECT id,nama_lapangan,harga_per_jam FROM lapangan WHERE status='aktif'");
                            while ($l = mysqli_fetch_assoc($lap)) {
                                echo '<option value="' . $l['id'] . '">' . htmlspecialchars($l['nama_lapangan']) . ' — Rp ' . number_format($l['harga_per_jam']) . '/jam</option>';
                            }
                            ?>
                        </select>
                    </label>

                    <label>Tanggal Booking
                        <input type="date" name="tanggal_booking" required>
                    </label>

                    <label>Jam Mulai
                        <input type="time" name="jam_mulai" required>
                    </label>

                    <label>Jam Selesai
                        <input type="time" name="jam_selesai" required>
                    </label>

                    <label>Upload Bukti Pembayaran (opsional)
                        <input type="file" name="bukti_pembayaran" accept="image/*">
                    </label>

                    <div class="full">
                        <button type="submit" class="btn-primary">Kirim Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="foot-left">
                <img src="/mnt/data/a728f4a7dbdeb486786ca67409f5d056.jpg" alt="logo" class="logo-small">
                <div>Futsal Booking • Jalan Contoh No.1 • Kota Contoh</div>
            </div>
            <div class="foot-right">
                <div>Contact: 0812-3456-7890</div>
                <div>&copy; <?= date('Y') ?> Futsal Booking</div>
            </div>
        </div>
    </footer>

</body>

</html>