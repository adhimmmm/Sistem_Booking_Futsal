# Sistem Booking Futsal

Sistem manajemen dan pemesanan lapangan futsal berbasis web yang mencakup panel admin untuk mengelola lapangan & memverifikasi booking, serta tampilan publik untuk pengguna melakukan booking.

## 📋 Fitur Utama

### Halaman Publik
- Melihat daftar lapangan yang tersedia
- Memilih tanggal dan jam booking
- Melakukan pemesanan lapangan
- Upload bukti pembayaran

### Panel Admin
- Login admin dengan autentikasi
- Dashboard dengan statistik (pending/valid/total booking)
- Kelola lapangan (CRUD + upload foto)
- Kelola booking (validasi, pending, hapus)
- Kontrol status lapangan (aktif/nonaktif)

## 📁 Struktur Direktori

```
Sistem_Booking_Futsal/
├── admin/                    # Panel admin
│   ├── login.php            # Halaman login admin
│   ├── dashboard.php        # Dashboard statistik
│   ├── lapangan_list.php    # Daftar lapangan
│   └── lapangan_edit.php    # Edit/tambah lapangan
├── css/                      # File stylesheet
├── includes/                 # File koneksi & fungsi
│   ├── db.php               # Koneksi database
│   └── session.php          # Session management
├── uploads/
│   └── lapangan/            # Foto lapangan
├── index.php                # Halaman publik utama
├── booking_submit.php       # Handler form booking
└── booking_success.php      # Konfirmasi booking
```

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP Native
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript
- **Server**: Apache (XAMPP/WAMP/LAMP)

## 📦 Setup / Instalasi

### Prasyarat
- XAMPP/WAMP/LAMP (PHP 7.4+ dan MySQL)
- Web browser

### Langkah Instalasi

**1. Clone repository**
```bash
git clone https://github.com/adhimmmm/Sistem_Booking_Futsal.git
```

**2. Pindahkan ke direktori web server**
- Letakkan folder project di `htdocs` (XAMPP) atau root webserver Anda

**3. Buat database MySQL**
```sql
CREATE DATABASE db_booking_futsal;
```

**4. Buat tabel yang diperlukan**

**Tabel Lapangan:**
```sql
CREATE TABLE lapangan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_lapangan VARCHAR(100) NOT NULL,
  deskripsi TEXT,
  foto VARCHAR(255),
  harga_per_jam INT NOT NULL,
  status ENUM('aktif','nonaktif') DEFAULT 'aktif',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Tabel Booking:**
```sql
CREATE TABLE booking (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_pemesan VARCHAR(100) NOT NULL,
  nomor_wa VARCHAR(20) NOT NULL,
  lapangan_id INT NOT NULL,
  tanggal_booking DATE NOT NULL,
  jam_mulai TIME NOT NULL,
  jam_selesai TIME NOT NULL,
  total_harga INT NOT NULL,
  bukti_pembayaran VARCHAR(255),
  status ENUM('pending','valid','ditolak') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Tabel Jadwal Terblokir:**
```sql
CREATE TABLE jadwal_terblokir (
  id INT AUTO_INCREMENT PRIMARY KEY,
  lapangan_id INT NOT NULL,
  tanggal DATE NOT NULL,
  jam_mulai TIME NOT NULL,
  jam_selesai TIME NOT NULL,
  booking_id INT NOT NULL
);
```

**Tabel Admin:**
```sql
CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama_lengkap VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**5. Konfigurasi database**

Edit file `includes/db.php`:
```php
<?php
$server = "localhost";
$username = "root";
$password = "";
$database = "db_booking_futsal";

$conn = mysqli_connect($server, $username, $password, $database);
?>
```

**6. Set permission folder upload**

Pastikan folder `uploads/lapangan/` memiliki permission write:
- **Windows/XAMPP**: Pastikan folder sudah ada
- **Linux**: `chmod 755 uploads/lapangan/` atau `chmod 777` untuk testing lokal

**7. Akses aplikasi**
- **Halaman Publik**: `http://localhost/Sistem_Booking_Futsal/index.php`
- **Panel Admin**: `http://localhost/Sistem_Booking_Futsal/admin/login.php`

## 👤 Penggunaan Admin

### Login
1. Akses panel admin melalui `admin/login.php`
2. Masukkan kredensial admin yang telah dibuat di database

### Kelola Lapangan
- **Tambah Lapangan**: Input nama, deskripsi, harga per jam, status, dan upload foto
- **Edit Lapangan**: Ubah informasi lapangan yang sudah ada
- **Hapus Lapangan**: Menghapus data lapangan beserta foto di server
- **Status Lapangan**: 
  - `aktif` → Tampil di halaman publik
  - `nonaktif` → Disembunyikan dari pengguna

### Dashboard
- Lihat statistik booking (pending, valid, total)
- Kelola dan validasi booking dari pengguna
- Verifikasi bukti pembayaran

## 🔒 Catatan Keamanan

Untuk penggunaan production, implementasikan:

1. **Enkripsi Password**
```php
// Saat registrasi/insert admin
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Saat login
if (password_verify($input_password, $hashed_password)) {
    // Login berhasil
}
```

2. **Prepared Statements**
   - Gunakan prepared statements untuk semua query database
   - Hindari SQL Injection

3. **Validasi Input**
   - Validasi tipe file upload (hanya jpg/png)
   - Batasi ukuran file (maks 2-5 MB)
   - Sanitasi semua input user

4. **HTTPS**
   - Gunakan SSL/TLS untuk koneksi aman

## 📱 Responsivitas

Aplikasi sudah menggunakan styling responsif untuk tampilan mobile. Lakukan testing di berbagai ukuran layar dan sesuaikan CSS jika diperlukan.

## 🚀 Fitur yang Bisa Dikembangkan

- [ ] Pencarian lapangan berdasarkan ketersediaan tanggal/jam
- [ ] Kalender visual untuk admin & pengguna
- [ ] Notifikasi email/WhatsApp otomatis setelah verifikasi
- [ ] Sistem registrasi dan login untuk pengguna
- [ ] Integrasi payment gateway (Midtrans, Stripe, dll)
- [ ] Laporan & statistik booking yang lebih detail
- [ ] Review dan rating lapangan

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan:

1. Fork repository ini
2. Buat branch fitur baru (`git checkout -b feature/FiturBaru`)
3. Commit perubahan (`git commit -m 'Menambahkan fitur baru'`)
4. Push ke branch (`git push origin feature/FiturBaru`)
5. Buat Pull Request

## 🐛 Troubleshooting

**Error koneksi database**
- Pastikan MySQL service aktif
- Cek konfigurasi di `includes/db.php`

**Upload foto gagal**
- Periksa permission folder `uploads/lapangan/`
- Pastikan ukuran file tidak melebihi batas PHP (`upload_max_filesize`)

**Halaman admin tidak bisa diakses**
- Cek session PHP sudah aktif
- Pastikan tabel admin sudah dibuat dan ada data admin



---

⭐ **Jika proyek ini bermanfaat, jangan lupa beri star!**
