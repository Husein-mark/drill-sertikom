@extends('layouts.app')

@section('title', $workshop->title . ' - WORKSHOPTIX')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Navigasi Balik -->
    <div class="mb-6">
        <a href="{{ route('catalog.index') }}" class="inline-flex items-center text-xs font-bold text-blue-700 hover:text-blue-900 bg-white border border-slate-300 px-3 py-1.5 rounded">
            &larr; Kembali ke Katalog Workshop
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Kolom Kiri: Detail Informasi Materi & Instruktur -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white border border-slate-300 rounded p-6 sm:p-8">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded bg-blue-50 text-blue-800 border border-blue-200">
                        [{{ $workshop->category }}]
                    </span>
                    @if($workshop->available_quota <= 0)
                        <span class="text-xs font-bold px-2.5 py-1 rounded bg-rose-50 text-rose-800 border border-rose-300">
                            [STATUS: KUOTA TERISI PENUH]
                        </span>
                    @else
                        <span class="text-xs font-bold px-2.5 py-1 rounded bg-emerald-50 text-emerald-800 border border-emerald-300">
                            [STATUS: PENDAFTARAN DIBUKA]
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mb-4 leading-tight">
                    {{ $workshop->title }}
                </h1>

                <!-- Rincian Parameter Workshop -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 bg-slate-50 border border-slate-200 rounded text-xs mb-6">
                    <div>
                        <span class="text-[10px] font-bold text-slate-500 uppercase block">Jadwal Tanggal</span>
                        <span class="font-bold text-slate-900 text-sm block mt-0.5">{{ $workshop->schedule_date->translatedFormat('d F Y') }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-500 uppercase block">Waktu Pelaksanaan</span>
                        <span class="font-bold text-slate-900 text-sm block mt-0.5">{{ $workshop->schedule_date->format('H:i') }} WIB</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-500 uppercase block">Durasi Sesi</span>
                        <span class="font-bold text-slate-900 text-sm block mt-0.5">{{ $workshop->duration_hours }} Jam Pelatihan</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-500 uppercase block">Kapasitas Kursi</span>
                        <span class="font-bold text-slate-900 text-sm block mt-0.5">{{ $workshop->quota }} Peserta Total</span>
                    </div>
                </div>

                <!-- Pemateri -->
                <div class="border-t border-b border-slate-200 py-4 mb-6">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">PROFIL INSTRUKTUR</span>
                    <div class="flex flex-col">
                        <span class="text-base font-bold text-slate-900">{{ $workshop->instructor_name }}</span>
                        <span class="text-xs text-slate-600 font-medium">{{ $workshop->instructor_title }}</span>
                    </div>
                </div>

                <!-- Deskripsi Lengkap -->
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">DESKRIPSI & SILABUS MATERI</h2>
                    <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded border border-slate-200">
                        {{ $workshop->description }}
                    </div>
                </div>

                <!-- Ketentuan Drill Sertifikasi -->
                <div class="mt-6 p-4 rounded bg-blue-50 border border-blue-200 text-xs text-blue-900">
                    <span class="font-bold block mb-1">[KETENTUAN PELATIHAN KOMPETENSI]</span>
                    <p class="leading-relaxed">
                        Setiap peserta akan mendapatkan sertifikat digital kelulusan workshop setelah menyelesaikan seluruh sesi materi dan evaluasi praktikum. Pembayaran yang sah diproses melalui integrasi Sandbox Midtrans.
                    </p>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Panel Aksi Pendaftaran & Biaya -->
        <div class="lg:col-span-4">
            <div class="bg-white border border-slate-300 rounded shadow-sm sticky top-24">
                <div class="p-6 border-b border-slate-200">
                    <span class="text-[11px] font-bold text-slate-400 uppercase block mb-1">BIAYA INVESTASI RESMI</span>
                    <div class="text-3xl font-black text-blue-800">
                        {{ $workshop->formatted_price }}
                    </div>
                    <span class="text-xs text-slate-500 block mt-1">Termasuk materi modul, akses lab, dan e-ticket</span>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Status Kuota -->
                    <div class="text-xs space-y-1.5 pb-2 border-b border-slate-100">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Total Kuota:</span>
                            <span class="font-semibold text-slate-800">{{ $workshop->quota }} Kursi</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Sisa Kuota Tersedia:</span>
                            <span class="font-bold {{ $workshop->available_quota <= 5 ? 'text-rose-600' : 'text-emerald-700' }}">
                                {{ $workshop->available_quota }} Kursi
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Tempat:</span>
                            <span class="font-semibold text-slate-800 text-right">{{ $workshop->location }}</span>
                        </div>
                    </div>

                    <!-- Tombol Aksi Berdasarkan Kondisi Autentikasi dan Pendaftaran -->
                    @if($isRegistered)
                        @if($existingRegistration->isPaid())
                            <div class="p-3 bg-emerald-50 border border-emerald-300 rounded text-center">
                                <span class="font-bold text-xs text-emerald-900 block mb-1">[STATUS: SUDAH TERDAFTAR & LUNAS]</span>
                                <p class="text-[11px] text-emerald-800 mb-3">Anda sudah memiliki tiket digital resmi untuk workshop ini.</p>
                                <a href="{{ route('registrations.ticket', $existingRegistration) }}" class="block w-full py-2 px-4 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded text-center">
                                    Buka Tiket Digital Resmi
                                </a>
                            </div>
                        @else
                            <div class="p-3 bg-amber-50 border border-amber-300 rounded text-center">
                                <span class="font-bold text-xs text-amber-900 block mb-1">[STATUS: MENUNGGU PEMBAYARAN]</span>
                                <p class="text-[11px] text-amber-800 mb-3">Pendaftaran Anda tercatat, mohon selesaikan transaksi.</p>
                                <a href="{{ route('registrations.payment', $existingRegistration) }}" class="block w-full py-2 px-4 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded text-center">
                                    Lanjutkan Pembayaran
                                </a>
                            </div>
                        @endif
                    @else
                        @if($workshop->available_quota <= 0)
                            <button disabled class="w-full py-3 px-4 bg-slate-300 text-slate-600 font-bold text-xs rounded cursor-not-allowed">
                                Pendaftaran Ditutup (Kuota Penuh)
                            </button>
                        @else
                            @auth
                                @if(auth()->user()->isPeserta())
                                    <a href="{{ route('registrations.create', $workshop) }}" class="block w-full py-3 px-4 bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs rounded text-center shadow-sm">
                                        Daftar Workshop Sekarang
                                    </a>
                                @else
                                    <div class="p-3 bg-slate-100 border border-slate-300 rounded text-center text-xs text-slate-700 font-medium">
                                        Anda sedang masuk sebagai [PANITIA]. Pendaftaran hanya untuk akun peserta.
                                    </div>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="block w-full py-3 px-4 bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs rounded text-center shadow-sm">
                                    Masuk untuk Mendaftar
                                </a>
                                <span class="block text-center text-[11px] text-slate-500">
                                    Belum punya akun? <a href="{{ route('register') }}" class="text-blue-700 font-bold hover:underline">Daftar Akun Baru</a>
                                </span>
                            @endauth
                        @endif
                    @endif

                    <div class="text-[11px] text-slate-500 border-t border-slate-100 pt-3 space-y-1">
                        <span class="font-bold text-slate-700 block">Jaminan Drill Sertifikasi:</span>
                        <p>- Tiket diterbitkan otomatis setelah verifikasi lunas.</p>
                        <p>- Data tersinkronisasi aman dengan backend Laravel.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
