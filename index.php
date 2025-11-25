<?php
// index.php
include 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Futsal Booking — Premium Court Reservation</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="Booking lapangan futsal online dengan mudah. Jadwal real-time, harga transparan, verifikasi cepat.">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <!-- NAVIGATION -->
    <header class="nav">
        <div class="container nav-inner">
            <a href="#hero" class="brand">
                FUTSAL<span style="color:#1F2937; font-weight: 400;">BOOKING</span>
            </a>
            <nav class="nav-links">
                <a href="#lapangan">Lapangan</a>
                <a href="#fitur">Keunggulan</a>
                <a href="#galeri">Galeri</a>
                <a href="#booking" class="btn-outline">BOOKING SEKARANG</a>
            </nav>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section id="hero" class="hero">
        <div class="hero-content container">
            <h1>
                Lapangan Futsal Terbaik
                <span>Booking Online Praktis & Cepat</span>
            </h1>
            <p>Pesan lapangan, cek jadwal real-time, dan pastikan tempat bermain tim Anda sudah teramankan. Proses tanpa ribet, tanpa perlu registrasi.</p>
            <div class="hero-cta">
                <a href="#lapangan" class="btn-primary">Lihat Lapangan</a>
                <a href="#booking" class="btn-secondary">Booking Langsung</a>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section id="fitur" class="container section features">
        <div class="section-head">
            <h2>Kemudahan Booking di Tempat Kami</h2>
            <p>Kami memastikan pengalaman booking yang mulus dengan fitur-fitur unggulan ini.</p>
        </div>

        <div class="cards">
            <article class="card">
                <div class="card-icon">📅</div>
                <h3>Jadwal Akurat</h3>
                <p>Ketersediaan jam diperbarui secara real-time. Langsung lihat dan pilih slot yang kosong tanpa khawatir bentrok.</p>
            </article>

            <article class="card">
                <div class="card-icon">💰</div>
                <h3>Harga Transparan</h3>
                <p>Tidak ada biaya tersembunyi. Total biaya dihitung otomatis saat Anda memilih jam bermain. Clear pricing untuk semua lapangan.</p>
            </article>

            <article class="card">
                <div class="card-icon">✅</div>
                <h3>Verifikasi Cepat</h3>
                <p>Unggah bukti bayar, dan admin kami akan memproses validasi booking Anda dengan cepat maksimal 2 jam.</p>
            </article>

            <article class="card">
                <div class="card-icon">💬</div>
                <h3>Layanan Terbaik</h3>
                <p>Kami selalu siap membantu. Hubungi admin kami melalui WhatsApp jika ada kendala atau pertanyaan.</p>
            </article>
        </div>
    </section>

    <!-- STADIUMS SECTION -->
    <section id="lapangan" class="container section stadiums">
        <div class="section-head">
            <h2>Pilihan Lapangan Premium</h2>
            <p>Kami menyediakan lapangan indoor dengan fasilitas dan kondisi terbaik untuk pengalaman bermain maksimal.</p>
        </div>

        <div class="stadium-grid">
            <?php
                            $q = mysqli_query($conn, "SELECT * FROM lapangan WHERE status='aktif' ORDER BY nama_lapangan ASC");

                            $i = 0;
                            while ($row = mysqli_fetch_assoc($q)) {
                                $harga = number_format($row['harga_per_jam'], 0, ',', '.');
                                $desc_snippet = substr($row['deskripsi'], 0, 100);

                                if (strlen($row['deskripsi']) > 100) {
                                    $desc_snippet .= '...';
                                }

                                // Ambil gambar dari database
                                $gambar = !empty($row['foto'])
                                    ? $row['foto']
                                    : 'assets/default_lapangan.jpg';

                echo '
            <div class="stadium-card">
                <div class="stadium-thumb" style="background-image:url(\'' . $gambar . '\')">
            <span class="stadium-badge">Premium</span>
        </div>
                <div class="stadium-body">
                    <h3>' . htmlspecialchars($row['nama_lapangan']) . '</h3>
                    <div class="stadium-rating">
                        <span class="stars">⭐⭐⭐⭐⭐</span>
                        <span class="rating-text">5.0 Rating</span>
                    </div>
                    <span class="muted">Rp ' . $harga . '<small>/jam</small></span>
                    <p class="small">' . htmlspecialchars($desc_snippet) . '</p>
                    <a class="btn-link" href="#booking">Pesan Sekarang →</a>
                </div>
            </div>';
                $i++;
            }

            if (mysqli_num_rows($q) == 0) {
                echo "<div style='grid-column: 1 / -1; text-align: center; padding: 60px 20px;'>
                        <p style='color: var(--gray); font-size: 18px;'>Saat ini belum ada lapangan yang tersedia.</p>
                      </div>";
            }
            ?>
        </div>
    </section>

    <!-- PROMO SECTION -->
    <section class="promo">
        <div class="container promo-inner">
            <div class="promo-text">
                <h2>Jangan Sampai Keduluan!</h2>
                <p>Amankan jadwal tim Anda sekarang juga sebelum slot terbaik terisi. Weekend dan prime time selalu ramai!</p>
            </div>
            <div class="promo-action">
                <a href="#booking" class="btn-primary">Booking Sekarang</a>
            </div>
        </div>
    </section>

    <!-- GALLERY SECTION -->
    <section id="galeri" class="container section gallery">
        <div class="section-head">
            <h2>Galeri Lapangan</h2>
            <p>Lihat suasana lapangan dan fasilitas kami yang nyaman dan profesional.</p>
        </div>
        <div class="gallery-grid">
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1602992709295-cfb8f52a2656?auto=format&fit=crop&w=800&q=80" alt="Lapangan Futsal Indoor">
                <div class="gallery-overlay">
                    <span>Lapangan Indoor Premium</span>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1574044195191-4e7826315570?auto=format&fit=crop&w=800&q=80" alt="Fasilitas Lengkap">
                <div class="gallery-overlay">
                    <span>Fasilitas Lengkap</span>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1549880338-65ddcdfd017b?auto=format&fit=crop&w=800&q=80" alt="Suasana Bermain">
                <div class="gallery-overlay">
                    <span>Suasana Bermain Nyaman</span>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1588764042898-1e4a6825c34e?auto=format&fit=crop&w=800&q=80" alt="Ruang Ganti">
                <div class="gallery-overlay">
                    <span>Ruang Ganti Bersih</span>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=800&q=80" alt="Pencahayaan Optimal">
                <div class="gallery-overlay">
                    <span>Pencahayaan Optimal</span>
                </div>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1589487391730-58f20eb2c308?auto=format&fit=crop&w=800&q=80" alt="Area Parkir">
                <div class="gallery-overlay">
                    <span>Area Parkir Luas</span>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS SECTION -->
    <section class="container section testimonials">
        <div class="section-head">
            <h2>Apa Kata Mereka</h2>
            <p>Pengalaman nyata dari para pemain yang sudah booking di tempat kami.</p>
        </div>

        <div class="testimonial-grid">
            <div class="testimonial-card">
                <div class="testimonial-quote">"</div>
                <p>Booking sangat mudah dan cepat! Lapangannya bersih, fasilitasnya lengkap. Recommended banget untuk yang cari tempat main futsal berkualitas.</p>
                <div class="testimonial-author">
                    <div class="author-avatar">AR</div>
                    <div class="author-info">
                        <strong>Andi Ramadhan</strong>
                        <span>Tim FC Malang</span>
                    </div>
                </div>
            </div>

            <div class="testimonial-card featured">
                <div class="testimonial-quote">"</div>
                <p>Sistemnya transparan, harga jelas, dan admin responsif. Gak perlu ribet daftar akun, langsung isi form dan bayar. Prosesnya cepat banget!</p>
                <div class="testimonial-author">
                    <div class="author-avatar">BW</div>
                    <div class="author-info">
                        <strong>Budi Wijaya</strong>
                        <span>Kapten Tim Warrior</span>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-quote">"</div>
                <p>Lapangan favorit kami untuk latihan rutin. Kondisi rumput sintetis selalu terjaga, pencahayaannya bagus. Worth it dengan harganya!</p>
                <div class="testimonial-author">
                    <div class="author-avatar">DP</div>
                    <div class="author-info">
                        <strong>Dimas Prasetyo</strong>
                        <span>Komunitas Futsal Arema</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SPONSOR SECTION -->
    <section class="container section sponsor">
        <div class="section-head" style="margin-bottom: 40px;">
            <h3>Didukung oleh Mitra Terbaik</h3>
        </div>
        <div class="sponsor-logos">
            <img src="https://upload.wikimedia.org/wikipedia/commons/2/20/Coca-Cola_logo.svg" alt="Coca Cola">
            <img src="https://upload.wikimedia.org/wikipedia/commons/a/a6/Logo_NIKE.svg" alt="Nike">
            <img src="https://upload.wikimedia.org/wikipedia/en/3/37/Adidas_logo.svg" alt="Adidas">
            <img src="https://upload.wikimedia.org/wikipedia/commons/3/3e/Ford_logo_flat.svg" alt="Ford">
        </div>
    </section>

    <!-- BOOKING SECTION -->
    <section id="booking" class="container section booking">
        <div class="booking-wrap">
            <div class="booking-info">
                <h2>Formulir Booking Cepat</h2>
                <p>Isi detail pemesanan Anda dengan lengkap. Pastikan tanggal dan jam tidak bentrok dengan jadwal lain sebelum mengirimkan formulir.</p>

                <ul class="info-list">
                    <li>
                        <strong>🚀 Tanpa Akun:</strong> Proses booking instan, tidak perlu registrasi atau login.
                    </li>
                    <li>
                        <strong>💳 Pembayaran:</strong> Transfer ke rekening yang tertera, wajib unggah bukti (JPG/PNG maks 5MB).
                    </li>
                    <li>
                        <strong>✅ Verifikasi:</strong> Setelah form terkirim, admin akan konfirmasi via WhatsApp maksimal 2 jam.
                    </li>
                    <li>
                        <strong>📱 Support:</strong> Butuh bantuan? Hubungi kami di WhatsApp: 0812-3456-7890
                    </li>
                </ul>

                <div class="payment-info">
                    <h4>Informasi Pembayaran</h4>
                    <div class="bank-details">
                        <p><strong>Bank BCA</strong><br>1234567890<br>A.n. Futsal Booking</p>
                        <p><strong>Bank Mandiri</strong><br>9876543210<br>A.n. Futsal Booking</p>
                    </div>
                </div>
            </div>

            <div class="booking-form-card">
                <form action="booking_submit.php" method="POST" enctype="multipart/form-data" class="form-grid" id="bookingForm">

                    <label class="full">
                        <span>Nama Pemesan <span class="required">*</span></span>
                        <input type="text" name="nama_pemesan" required placeholder="Masukkan nama lengkap Anda">
                    </label>

                    <label class="full">
                        <span>Nomor WhatsApp (Aktif) <span class="required">*</span></span>
                        <input type="text" name="nomor_wa" placeholder="Contoh: 6281234567890" required pattern="[0-9]{10,15}">
                        <small class="field-hint">Format: 628xxxxxxxxxx (tanpa +)</small>
                    </label>

                    <label class="full">
                        <span>Pilih Lapangan <span class="required">*</span></span>
                        <select name="lapangan_id" required id="lapanganSelect">
                            <option value="">-- Pilih Lapangan --</option>
                            <?php
                            $lap = mysqli_query($conn, "SELECT id,nama_lapangan,harga_per_jam FROM lapangan WHERE status='aktif' ORDER BY nama_lapangan ASC");
                            while ($l = mysqli_fetch_assoc($lap)) {
                                echo '<option value="' . $l['id'] . '" data-harga="' . $l['harga_per_jam'] . '">'
                                    . htmlspecialchars($l['nama_lapangan'])
                                    . ' — Rp ' . number_format($l['harga_per_jam'], 0, ',', '.') . '/jam</option>';
                            }
                            ?>
                        </select>
                    </label>

                    <label>
                        <span>Tanggal Booking <span class="required">*</span></span>
                        <input type="date" name="tanggal_booking" required id="tanggalBooking" min="<?= date('Y-m-d') ?>">
                    </label>

                    <label>
                        <span>Jumlah Jam <span class="required">*</span></span>
                        <select name="durasi" id="durasiSelect" required>
                            <option value="">Pilih durasi</option>
                            <option value="1">1 Jam</option>
                            <option value="2">2 Jam</option>
                            <option value="3">3 Jam</option>
                            <option value="4">4 Jam</option>
                        </select>
                    </label>

                    <label>
                        <span>Jam Mulai <span class="required">*</span></span>
                        <input type="time" name="jam_mulai" required id="jamMulai">
                    </label>

                    <label>
                        <span>Jam Selesai <span class="required">*</span></span>
                        <input type="time" name="jam_selesai" required id="jamSelesai" readonly>
                    </label>

                    <div class="total-payment">
                        <div class="payment-label">Total Pembayaran:</div>
                        <div class="payment-amount" id="totalPayment">Rp 0</div>
                    </div>

                    <label class="full">
                        <span>Upload Bukti Pembayaran <span class="optional">(Opsional, tapi disarankan)</span></span>
                        <input type="file" name="bukti_pembayaran" accept="image/jpeg,image/png,image/jpg" id="buktiFile">
                        <small class="field-hint">Format: JPG/PNG, Maksimal 5MB</small>
                    </label>

                    <div class="full">
                        <button type="submit" class="btn-primary btn-block">
                            <span>Kirim Permintaan Booking</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container footer-inner">
            <div class="footer-col">
                <h4 class="footer-brand">FUTSAL<span>BOOKING</span></h4>
                <p>Platform booking lapangan futsal terpercaya dengan sistem yang mudah dan transparan.</p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook">📘</a>
                    <a href="#" aria-label="Instagram">📷</a>
                    <a href="#" aria-label="Twitter">🐦</a>
                </div>
            </div>

            <div class="footer-col">
                <h5>Navigasi</h5>
                <ul class="footer-links">
                    <li><a href="#lapangan">Lapangan</a></li>
                    <li><a href="#fitur">Keunggulan</a></li>
                    <li><a href="#galeri">Galeri</a></li>
                    <li><a href="#booking">Booking</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5>Kontak</h5>
                <ul class="footer-links">
                    <li>📍 Jalan Contoh No.1, Malang</li>
                    <li>📞 0812-3456-7890</li>
                    <li>✉️ info@futsalbooking.com</li>
                    <li>🕒 Buka 08:00 - 23:00</li>
                </ul>
            </div>

            <div class="footer-col">
                <h5>Informasi</h5>
                <ul class="footer-links">
                    <li><a href="#">Syarat & Ketentuan</a></li>
                    <li><a href="#">Kebijakan Privasi</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Tentang Kami</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <p>&copy; <?= date('Y') ?> Futsal Booking. All rights reserved. Made with ⚽ in Malang.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript for form calculation -->
    <script>
        const lapanganSelect = document.getElementById('lapanganSelect');
        const durasiSelect = document.getElementById('durasiSelect');
        const jamMulai = document.getElementById('jamMulai');
        const jamSelesai = document.getElementById('jamSelesai');
        const totalPayment = document.getElementById('totalPayment');
        const tanggalBooking = document.getElementById('tanggalBooking');

        function calculateTotal() {
            const selectedOption = lapanganSelect.options[lapanganSelect.selectedIndex];
            const harga = parseInt(selectedOption.getAttribute('data-harga')) || 0;
            const durasi = parseInt(durasiSelect.value) || 0;

            const total = harga * durasi;
            totalPayment.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        function calculateEndTime() {
            if (jamMulai.value && durasiSelect.value) {
                const [hours, minutes] = jamMulai.value.split(':').map(Number);
                const durasi = parseInt(durasiSelect.value);

                let endHours = hours + durasi;
                let endMinutes = minutes;

                if (endHours >= 24) endHours = endHours - 24;

                jamSelesai.value = `${String(endHours).padStart(2, '0')}:${String(endMinutes).padStart(2, '0')}`;
            }
        }

        lapanganSelect.addEventListener('change', calculateTotal);
        durasiSelect.addEventListener('change', () => {
            calculateTotal();
            calculateEndTime();
        });
        jamMulai.addEventListener('change', calculateEndTime);

        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        tanggalBooking.setAttribute('min', today);

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>

</body>

</html>