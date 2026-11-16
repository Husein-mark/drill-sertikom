<?php

namespace Database\Seeders;

use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Panitia
        $panitia = User::create([
            'name' => 'Panitia Sertifikasi IT',
            'email' => 'panitia@workshoptix.com',
            'role' => 'panitia',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
        ]);

        // 2. Akun Peserta Utama
        $peserta1 = User::create([
            'name' => 'Ahmad Rizky Pratama',
            'email' => 'peserta@workshoptix.com',
            'role' => 'peserta',
            'phone' => '089876543210',
            'password' => Hash::make('password'),
        ]);

        // 3. Akun Peserta Tambahan
        $peserta2 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@workshoptix.com',
            'role' => 'peserta',
            'phone' => '085678901234',
            'password' => Hash::make('password'),
        ]);

        $peserta3 = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@workshoptix.com',
            'role' => 'peserta',
            'phone' => '087812345678',
            'password' => Hash::make('password'),
        ]);

        $peserta4 = User::create([
            'name' => 'Fajar Hidayat',
            'email' => 'fajar@workshoptix.com',
            'role' => 'peserta',
            'phone' => '081399887766',
            'password' => Hash::make('password'),
        ]);

        $peserta5 = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi@workshoptix.com',
            'role' => 'peserta',
            'phone' => '082155443322',
            'password' => Hash::make('password'),
        ]);

        // 4. Data Workshop
        $workshop1 = Workshop::create([
            'title' => 'Fullstack Web Development Modern dengan Laravel & Tailwind CSS',
            'slug' => 'fullstack-web-development-laravel-tailwind',
            'category' => 'Web Development',
            'instructor_name' => 'Ir. Hendra Gunawan, S.Kom., M.T.',
            'instructor_title' => 'Senior Lead Software Architect di Tech Nusantara',
            'description' => 'Pelatihan intensif pengembangan aplikasi web berbasis arsitektur modern MVC, integrasi pembayaran digital, otorisasi peran pengguna, dan optimalisasi basis data untuk kesiapan sertifikasi profesi kompetensi RPL.',
            'schedule_date' => now()->addDays(5)->setHour(9)->setMinute(0),
            'duration_hours' => 4,
            'location' => 'Lab Komputer RPL 1 & Streaming Zoom',
            'price' => 75000,
            'quota' => 40,
            'is_active' => true,
        ]);

        $workshop2 = Workshop::create([
            'title' => 'Cyber Security Fundamentals: Web Defense & Vulnerability Assessment',
            'slug' => 'cyber-security-fundamentals-web-defense',
            'category' => 'Cyber Security',
            'instructor_name' => 'Bayu Wicaksono, CEH, OSCP',
            'instructor_title' => 'Information Security Consultant',
            'description' => 'Mempelajari proteksi kerentanan web OWASP Top 10, teknik audit keamanan kode sumber, pencegahan SQL Injection, XSS, CSRF, serta manajemen identitas dan kontrol akses pada aplikasi berbasis web.',
            'schedule_date' => now()->addDays(7)->setHour(13)->setMinute(30),
            'duration_hours' => 3,
            'location' => 'Auditorium Lantai 2 SMK PESAT',
            'price' => 85000,
            'quota' => 35,
            'is_active' => true,
        ]);

        $workshop3 = Workshop::create([
            'title' => 'Automated Testing & CI/CD Pipeline untuk Aplikasi Skala Menengah',
            'slug' => 'automated-testing-ci-cd-pipeline',
            'category' => 'DevOps',
            'instructor_name' => 'Dimas Prasetyo, M.Cs.',
            'instructor_title' => 'DevOps Specialist di Cloud ID',
            'description' => 'Workshop terstruktur mengenai pembuatan Unit Testing, Feature Testing dengan Pest/PHPUnit, simulasi integrasi pipeline GitHub Actions, dan deployment otomatis untuk standar industri software house.',
            'schedule_date' => now()->addDays(10)->setHour(9)->setMinute(0),
            'duration_hours' => 4,
            'location' => 'Lab Komputer RPL 2',
            'price' => 60000,
            'quota' => 30,
            'is_active' => true,
        ]);

        $workshop4 = Workshop::create([
            'title' => 'Membangun RESTful API Terstandarisasi dengan Dokumentasi Swagger',
            'slug' => 'restful-api-terstandarisasi-swagger',
            'category' => 'Backend Development',
            'instructor_name' => 'Rina Kartika, S.T.',
            'instructor_title' => 'Senior Backend Engineer',
            'description' => 'Kuasai pembuatan API berbasis REST, serialisasi data Eloquent Resource, penanganan otentikasi Sanctum/JWT, rate limiting, error formatting, dan dokumentasi otomatis OpenAPI / Swagger.',
            'schedule_date' => now()->addDays(12)->setHour(13)->setMinute(0),
            'duration_hours' => 3,
            'location' => 'Lab Komputer Multimedia',
            'price' => 50000,
            'quota' => 45,
            'is_active' => true,
        ]);

        $workshop5 = Workshop::create([
            'title' => 'Desain Sistem Antarmuka Pengguna Berbasis Komponen Reusable',
            'slug' => 'desain-sistem-antarmuka-pengguna-reusable',
            'category' => 'UI/UX Design',
            'instructor_name' => 'Aditya Pratama, S.Ds.',
            'instructor_title' => 'Product Designer & UI Specialist',
            'description' => 'Panduan komprehensif merancang antarmuka profesional yang aksesibel, mematuhi prinsip hierarki visual, micro-interactions, layout grid adaptif, dan konsistensi token desain tanpa bergantung pada aset eksternal berlebihan.',
            'schedule_date' => now()->addDays(14)->setHour(9)->setMinute(30),
            'duration_hours' => 3,
            'location' => 'Ruang Teori 1 & Online',
            'price' => 45000,
            'quota' => 50,
            'is_active' => true,
        ]);

        // 5. Data Pendaftaran Sampel
        // Pendaftaran 1: Ahmad Rizky -> Workshop 1 (Lunas / Tiket Terbit)
        $reg1 = Registration::create([
            'registration_number' => 'REG-20261116-A101',
            'ticket_code' => 'TIX-20261116-W1A9872',
            'user_id' => $peserta1->id,
            'workshop_id' => $workshop1->id,
            'amount' => $workshop1->price,
            'status' => 'paid',
            'snap_token' => 'SNAP-SANDBOX-W1A9872X',
            'payment_type' => 'midtrans_sandbox_qris',
            'payment_reference' => 'MIDTRANS-SETTLE-001',
            'paid_at' => now()->subHours(2),
            'notes' => 'Pembayaran via QRIS Sandbox telah terverifikasi.',
        ]);

        // Pendaftaran 2: Budi Santoso -> Workshop 1 (Pending)
        $reg2 = Registration::create([
            'registration_number' => 'REG-20261116-B202',
            'ticket_code' => null,
            'user_id' => $peserta2->id,
            'workshop_id' => $workshop1->id,
            'amount' => $workshop1->price,
            'status' => 'pending',
            'snap_token' => 'SNAP-SANDBOX-B202PEND',
            'payment_type' => 'bank_transfer',
            'payment_reference' => 'MIDTRANS-PEND-002',
            'paid_at' => null,
            'notes' => 'Menunggu verifikasi pembayaran.',
        ]);

        // Pendaftaran 3: Siti Nurhaliza -> Workshop 2 (Lunas)
        $reg3 = Registration::create([
            'registration_number' => 'REG-20261116-C303',
            'ticket_code' => 'TIX-20261116-W2C3031',
            'user_id' => $peserta3->id,
            'workshop_id' => $workshop2->id,
            'amount' => $workshop2->price,
            'status' => 'paid',
            'snap_token' => 'SNAP-SANDBOX-W2C3031Y',
            'payment_type' => 'gopay',
            'payment_reference' => 'MIDTRANS-SETTLE-003',
            'paid_at' => now()->subDay(),
            'notes' => 'Pembayaran via GoPay terverifikasi.',
        ]);

        // Pendaftaran 4: Fajar Hidayat -> Workshop 3 (Lunas)
        $reg4 = Registration::create([
            'registration_number' => 'REG-20261116-D404',
            'ticket_code' => 'TIX-20261116-W3D4042',
            'user_id' => $peserta4->id,
            'workshop_id' => $workshop3->id,
            'amount' => $workshop3->price,
            'status' => 'paid',
            'snap_token' => 'SNAP-SANDBOX-W3D4042Z',
            'payment_type' => 'bank_transfer',
            'payment_reference' => 'MIDTRANS-SETTLE-004',
            'paid_at' => now()->subHours(5),
            'notes' => 'Transfer BCA Sandbox terverifikasi.',
        ]);

        // Pendaftaran 5: Dewi Lestari -> Workshop 4 (Pending)
        $reg5 = Registration::create([
            'registration_number' => 'REG-20261116-E505',
            'ticket_code' => null,
            'user_id' => $peserta5->id,
            'workshop_id' => $workshop4->id,
            'amount' => $workshop4->price,
            'status' => 'pending',
            'snap_token' => 'SNAP-SANDBOX-E505PEND',
            'payment_type' => 'qris',
            'payment_reference' => 'MIDTRANS-PEND-005',
            'paid_at' => null,
            'notes' => 'Menunggu penyelesaian di kasir simulasi.',
        ]);
    }
}
