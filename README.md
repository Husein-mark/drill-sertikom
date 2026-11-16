# WORKSHOPTIX
### Sistem Pendaftaran dan Tiket Digital Workshop IT
**LKPD • XI RPL • DRILL PRA SERTIFIKASI KOMPETENSI KEJURUAN**  
**SMK PESAT IT XPRO - REKAYASA PERANGKAT LUNAK**

---

## 1. Ringkasan Proyek (Brief)
Aplikasi berbasis framework Laravel untuk memfasilitasi panitia penyelenggara workshop IT sekolah dalam mengelola kelas pelatihan dan memungkinkan peserta untuk:
- Memilih workshop IT berbayar yang diminati.
- Mendaftar akun dan mengisi formulir pendaftaran resmi (dengan proteksi terhadap pendaftaran ganda).
- Membayar tiket melalui integrasi gateway pembayaran Midtrans Sandbox (nominal terproteksi di sisi server).
- Memverifikasi status pelunasan transaksi melalui backend Laravel.
- Mengakses dan mencetak tiket digital resmi (e-ticket) yang dilengkapi kode tiket unik, jadwal, dan identitas peserta hanya setelah pembayaran lunas.
- Menguji optimasi performa basis data melalui instrumen pembanding **Eager Loading vs N+1 Problem**.

---

## 2. Struktur Tim & Pembagian Tugas (Backlog Singkat)

| Tahapan | Uraian Tugas | Modul Terkait |
|---|---|---|
| **01. Rancang & Skema** | Perancangan ERD, skema database (users, workshops, registrations), dan konfigurasi project. | Database Migration, Model, Seeder |
| **02. Tampilan Blade** | Pembuatan layout Blade terpadu, antarmuka responsif tanpa icon, styling warna solid yang konsisten. | Blade View Layout, Catalog, Ticket, Auth |
| **03. Fitur Inti & Otorisasi** | Implementasi arsitektur MVC, autentikasi (login/register/logout), middleware role panitia/peserta, proteksi pendaftaran ganda. | AuthController, EnsureUserRole, RegistrationController |
| **04. Pembayaran Midtrans** | Integrasi API Midtrans Snap Sandbox, simulasi verifikasi webhook/backend, penerbitan kode tiket setelah lunas. | MidtransService, PaymentController |
| **05. Analisis Database N+1** | Pembuatan instrumen uji komparasi query: perbandingan jumlah query sebelum dan sesudah Eager Loading `with(['user', 'workshop'])`. | RegistrationManagerController, Query Log |
| **06. Pengujian & Demo** | Penulisan automated feature test suite (Pest) dan skenario demonstrasi end-to-end. | Pest Feature Tests, Manual Verification |

---

## 3. Skema Data (Database Schema)

### A. Tabel `users`
- `id` (Primary Key, unsigned bigint)
- `name` (string)
- `email` (string, unique)
- `phone` (string, nullable)
- `role` (enum/string: `panitia` | `peserta`, default: `peserta`)
- `password` (string, hashed)
- `timestamps`

### B. Tabel `workshops`
- `id` (Primary Key, unsigned bigint)
- `title` (string)
- `slug` (string, unique)
- `category` (string, e.g. Web Development, Cyber Security)
- `instructor_name` (string)
- `instructor_title` (string, nullable)
- `description` (text)
- `schedule_date` (datetime)
- `duration_hours` (unsigned integer)
- `location` (string)
- `price` (unsigned integer, nominal server)
- `quota` (unsigned integer)
- `is_active` (boolean, default: true)
- `timestamps`

### C. Tabel `registrations`
- `id` (Primary Key, unsigned bigint)
- `registration_number` (string, unique, format: `REG-YYYYMMDD-XXXXXX`)
- `ticket_code` (string, unique, nullable, terbit saat lunas: `TIX-YYYYMMDD-XXXXXXXX`)
- `user_id` (foreignId -> users, cascade on delete)
- `workshop_id` (foreignId -> workshops, cascade on delete)
- `amount` (unsigned integer, nominal diambil dari server)
- `status` (string: `pending`, `paid`, `failed`, `cancelled`, default: `pending`)
- `snap_token` (string, nullable)
- `payment_type` (string, nullable)
- `payment_reference` (string, nullable)
- `paid_at` (timestamp, nullable)
- `notes` (text, nullable)
- `timestamps`
- **Unique Constraint:** `['user_id', 'workshop_id']` (Mencegah peserta mendaftar lebih dari satu kali pada workshop yang sama).

---

## 4. Akun Default Pengujian (Seeder)

Aplikasi telah dilengkapi seeder akun siap pakai:

| Peran Akun | Alamat Email | Kata Sandi | Deskripsi Hak Akses |
|---|---|---|---|
| **Panitia** | `panitia@workshoptix.com` | `password` | Akses penuh dashboard, CRUD workshop, verifikasi transaksi manual, dan analisis query N+1. |
| **Peserta 1** | `peserta@workshoptix.com` | `password` | Memiliki 1 transaksi lunas (tiket sudah aktif) dan dapat mendaftar workshop lainnya. |
| **Peserta 2** | `budi@workshoptix.com` | `password` | Memiliki 1 transaksi pending (menunggu simulasi pembayaran). |

---

## 5. Panduan Instalasi & Menjalankan Aplikasi

### Langkah 1: Kebutuhan Sistem
- PHP >= 8.3 dengan ekstensi PDO (SQLite / MySQL), OpenSSL, Mbstring, cURL, GD.
- Composer >= 2.x
- Node.js >= 18.x & NPM

### Langkah 2: Instalasi Dependensi
```bash
composer install
npm install
```

### Langkah 3: Konfigurasi Lingkungan (`.env`)
Salin file `.env.example` ke `.env` (jika belum ada):
```bash
cp .env.example .env
php artisan key:generate
```

Konfigurasi Midtrans Sandbox pada `.env`:
```env
MIDTRANS_SERVER_KEY=SB-Mid-server-test-key-demo
MIDTRANS_CLIENT_KEY=SB-Mid-client-test-key-demo
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

### Langkah 4: Migrasi & Seeding Basis Data
```bash
php artisan migrate:fresh --seed
```

### Langkah 5: Kompilasi Aset Tampilan (Tailwind CSS)
```bash
npm run build
```

### Langkah 6: Jalankan Server Lokal
```bash
php artisan serve
```
Aplikasi dapat diakses melalui peramban web pada tautan: `http://localhost:8000`

---

## 6. Skenario Uji Coba & Demonstrasi (Demo Checklist)

### Skenario 1: Alur Pemesanan & Pembayaran Tiket (Peserta)
1. Buka `http://localhost:8000/login`, masuk menggunakan akun `peserta@workshoptix.com` (password: `password`).
2. Masuk ke halaman **Katalog Workshop**.
3. Pilih salah satu workshop yang belum pernah didaftarkan (misal: *Cyber Security Fundamentals*).
4. Klik tombol **Detail & Daftar**, lalu klik **Daftar Workshop Sekarang**.
5. Pada halaman konfirmasi, centang persetujuan syarat dan klik **Lanjutkan ke Pembayaran**.
6. Pada halaman pembayaran, nominal tagihan secara otomatis ditarik dari server. Status awal transaksi adalah `PENDING`.
7. Klik tombol **Simulasikan Pembayaran Berhasil &rarr;** (mensimulasikan pelunasan pada Midtrans Sandbox).
8. Sistem di sisi backend akan memverifikasi status pembayaran, mengubah status menjadi `PAID`, dan mencetak **Kode Tiket Digital Unik** (`TIX-...`).
9. Halaman tiket digital terbuka dan siap dicetak.

### Skenario 2: Pengujian Validasi Pendaftaran Ganda
1. Masih dalam akun peserta yang sama, coba buka kembali halaman workshop yang baru saja Anda bayar/daftarkan.
2. Tombol akan mendeteksi status pendaftaran sebelumnya dan mencegah pendaftaran ulang.
3. Jika form pendaftaran dikirim paksa melalui URL langsung, controller dan unique constraint basis data akan menolak pendaftaran dan mengembalikan pesan peringatan: *"Pendaftaran Ditolak: Anda sudah terdaftar di workshop ini sebelumnya"*.

### Skenario 3: Pengujian Otorisasi & Akses Tiket
1. Coba salin URL tiket milik akun lain saat login sebagai akun berbeda.
2. Sistem akan memvalidasi kepemilikan data melalui middleware dan policy otorisasi, lalu mengembalikan status `403 Forbidden` (Akses ditolak).
3. Tiket pada transaksi yang belum lunas (status `pending`) tidak dapat dibuka dan akan diarahkan kembali ke halaman pembayaran.

### Skenario 4: Pembuktian Optimasi Query N+1 vs Eager Loading (Panitia)
1. Keluar dari akun peserta, lalu masuk dengan akun panitia: `panitia@workshoptix.com` (password: `password`).
2. Pada navigasi atas, pilih menu **Pendaftar & Uji N+1** (`/panitia/pendaftar`).
3. Perhatikan panel **INSTRUMEN EVALUASI KOMPETENSI DATABASE LARAVEL**:
   - **Mode Eager Loading**: Mengeksekusi query dengan klausa `with(['user', 'workshop'])`. Hasilnya sistem hanya menjalankan **3 query SQL** untuk seluruh data pendaftar.
   - Klik tombol **[Mode Simulasi N+1 Problem]**: Sistem memuat relasi secara bertahap (lazy loading) pada setiap baris data pendaftar. Jumlah query SQL melonjak drastis sebanding dengan jumlah baris data (1 query induk + N query users + N query workshops).
4. Klik pada accordion detail query untuk melihat sintaks SQL asli dan durasi eksekusinya.

---

## 7. Pengujian Otomatis (Automated Test Suite)

Aplikasi memiliki tes otomatis lengkap menggunakan Pest PHP:
```bash
php artisan test
```

Seluruh 11 pengujian fitur mencakup:
- Katalog dan detail workshop publik.
- Registrasi akun baru dan autentikasi login berdasarkan peran.
- Perlindungan nominal harga di sisi server.
- Penolakan pendaftaran ganda (duplicate prevention).
- Larangan membuka tiket sebelum berstatus lunas.
- Simulasi pembayaran sandbox dan penerbitan tiket digital.
- Pencegahan akses tiket milik peserta lain (otorisasi 403).
- Manajemen CRUD workshop oleh panitia.
- Evaluasi komparasi query N+1 vs Eager Loading.
