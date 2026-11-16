<?php

use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->panitia = User::create([
        'name' => 'Panitia IT',
        'email' => 'panitia@test.com',
        'role' => 'panitia',
        'password' => bcrypt('password'),
    ]);

    $this->peserta = User::create([
        'name' => 'Peserta Satu',
        'email' => 'peserta@test.com',
        'role' => 'peserta',
        'phone' => '081234567890',
        'password' => bcrypt('password'),
    ]);

    $this->pesertaDua = User::create([
        'name' => 'Peserta Dua',
        'email' => 'pesertadua@test.com',
        'role' => 'peserta',
        'phone' => '089876543210',
        'password' => bcrypt('password'),
    ]);

    $this->workshop = Workshop::create([
        'title' => 'Workshop Laravel 12 & Midtrans',
        'slug' => 'workshop-laravel-12-midtrans',
        'category' => 'Web Development',
        'instructor_name' => 'Guru Penguji RPL',
        'instructor_title' => 'Assessor LSP',
        'description' => 'Materi pengujian sertifikasi profesi kompetensi kejuruan.',
        'schedule_date' => now()->addDays(7),
        'duration_hours' => 4,
        'location' => 'Lab Komputer 1',
        'price' => 75000,
        'quota' => 20,
        'is_active' => true,
    ]);
});

test('pengunjung dapat melihat katalog dan detail workshop', function () {
    $response = $this->get(route('catalog.index'));
    $response->assertStatus(200);
    $response->assertSee('Workshop Laravel 12 & Midtrans');

    $detailResponse = $this->get(route('catalog.show', $this->workshop));
    $detailResponse->assertStatus(200);
    $detailResponse->assertSee('Guru Penguji RPL');
    $detailResponse->assertSee('Rp 75.000');
});

test('pengguna dapat mendaftar akun baru dan login sesuai peran', function () {
    $registerResponse = $this->post(route('register.post'), [
        'name' => 'Siswa Baru',
        'email' => 'siswa@test.com',
        'phone' => '085512345678',
        'role' => 'peserta',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $registerResponse->assertRedirect(route('catalog.index'));
    $this->assertAuthenticated();

    $this->post(route('logout'));
    $this->assertGuest();

    $loginResponse = $this->post(route('login.post'), [
        'email' => 'panitia@test.com',
        'password' => 'password',
    ]);

    $loginResponse->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticated();
});

test('peserta dapat mendaftar workshop dan nominal harga diproteksi server', function () {
    $response = $this->actingAs($this->peserta)
        ->post(route('registrations.store', $this->workshop), [
            'agreement' => 1,
            'notes' => 'Harap konfirmasi segera',
        ]);

    $registration = Registration::where('user_id', $this->peserta->id)
        ->where('workshop_id', $this->workshop->id)
        ->first();

    expect($registration)->not->toBeNull();
    expect($registration->amount)->toBe(75000);
    expect($registration->status)->toBe('pending');
    expect($registration->ticket_code)->toBeNull();

    $response->assertRedirect(route('registrations.payment', $registration));
});

test('sistem menolak pendaftaran ganda pada workshop yang sama oleh peserta yang sama', function () {
    // Pendaftaran pertama
    $this->actingAs($this->peserta)
        ->post(route('registrations.store', $this->workshop), [
            'agreement' => 1,
        ]);

    // Pendaftaran kedua (ganda) harus ditolak
    $secondAttempt = $this->actingAs($this->peserta)
        ->post(route('registrations.store', $this->workshop), [
            'agreement' => 1,
        ]);

    $secondAttempt->assertRedirect(route('registrations.index'));
    $secondAttempt->assertSessionHas('error');

    expect(Registration::where('user_id', $this->peserta->id)->count())->toBe(1);
});

test('peserta tidak dapat membuka tiket jika status belum lunas', function () {
    $registration = Registration::create([
        'user_id' => $this->peserta->id,
        'workshop_id' => $this->workshop->id,
        'amount' => $this->workshop->price,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->peserta)
        ->get(route('registrations.ticket', $registration));

    $response->assertRedirect(route('registrations.payment', $registration));
    $response->assertSessionHas('error');
});

test('simulasi pembayaran backend memverifikasi status lunas dan menerbitkan tiket digital', function () {
    $registration = Registration::create([
        'user_id' => $this->peserta->id,
        'workshop_id' => $this->workshop->id,
        'amount' => $this->workshop->price,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->peserta)
        ->post(route('payments.simulate', $registration), [
            'payment_method' => 'Midtrans Sandbox QRIS',
        ]);

    $registration->refresh();

    expect($registration->isPaid())->toBeTrue();
    expect($registration->ticket_code)->not->toBeNull();
    expect($registration->ticket_code)->toStartWith('TIX-');

    $response->assertRedirect(route('registrations.ticket', $registration));

    // Sekarang tiket digital dapat dibuka
    $ticketResponse = $this->actingAs($this->peserta)
        ->get(route('registrations.ticket', $registration));

    $ticketResponse->assertStatus(200);
    $ticketResponse->assertSee($registration->ticket_code);
    $ticketResponse->assertSee('Peserta Satu');
    $ticketResponse->assertSee('Workshop Laravel 12 & Midtrans');
});

test('peserta lain dilarang mengakses tiket yang bukan miliknya', function () {
    $registration = Registration::create([
        'user_id' => $this->peserta->id,
        'workshop_id' => $this->workshop->id,
        'amount' => $this->workshop->price,
        'status' => 'paid',
        'ticket_code' => 'TIX-VALID-1234',
    ]);

    // Peserta dua mencoba mengakses tiket milik peserta satu
    $response = $this->actingAs($this->pesertaDua)
        ->get(route('registrations.ticket', $registration));

    $response->assertStatus(403);
});

test('panitia dapat mengelola workshop CRUD dan melihat daftar pendaftar', function () {
    $response = $this->actingAs($this->panitia)
        ->get(route('admin.dashboard'));
    $response->assertStatus(200);

    // Panitia buat workshop baru
    $createResponse = $this->actingAs($this->panitia)
        ->post(route('admin.workshops.store'), [
            'title' => 'Workshop Cloud DevOps Kubernetes',
            'category' => 'DevOps',
            'instructor_name' => 'Instruktur LSP',
            'instructor_title' => 'DevOps Engineer',
            'schedule_date' => now()->addDays(15)->format('Y-m-d H:i:s'),
            'duration_hours' => 3,
            'location' => 'Lab Komputer 2',
            'price' => 80000,
            'quota' => 25,
            'description' => 'Materi deployment dan cluster manajemen.',
            'is_active' => 1,
        ]);

    $createResponse->assertRedirect(route('admin.workshops.index'));
    $this->assertDatabaseHas('workshops', ['title' => 'Workshop Cloud DevOps Kubernetes']);
});

test('halaman pendaftar dapat menguji perbandingan N+1 query vs Eager Loading', function () {
    // Buat data pendaftaran
    Registration::create([
        'user_id' => $this->peserta->id,
        'workshop_id' => $this->workshop->id,
        'amount' => $this->workshop->price,
        'status' => 'paid',
        'ticket_code' => 'TIX-A1',
    ]);

    // Mode Eager Loading
    $eagerResponse = $this->actingAs($this->panitia)
        ->get(route('admin.registrations.index', ['mode' => 'eager']));

    $eagerResponse->assertStatus(200);
    $eagerResponse->assertSee('DENGAN EAGER LOADING (OPTIMAL)');

    // Mode Lazy (N+1)
    $lazyResponse = $this->actingAs($this->panitia)
        ->get(route('admin.registrations.index', ['mode' => 'lazy']));

    $lazyResponse->assertStatus(200);
    $lazyResponse->assertSee('TANPA EAGER LOADING (N+1 QUERY PROBLEM)');
});
