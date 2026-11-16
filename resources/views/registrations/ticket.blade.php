@extends('layouts.app')

@section('title', 'Tiket Digital Resmi - ' . $registration->ticket_code . ' - WORKSHOPTIX')

@push('head')
<style>
    @media print {
        header, nav, footer, .no-print {
            display: none !important;
        }
        body {
            background-color: white !important;
        }
        .ticket-container {
            border: 2px solid #000 !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    <!-- Tombol Aksi di Luar Tiket -->
    <div class="mb-6 flex flex-wrap justify-between items-center gap-3 no-print">
        <a href="{{ route('registrations.index') }}" class="text-xs font-bold text-blue-700 hover:text-blue-900 bg-white border border-slate-300 px-3 py-1.5 rounded">
            &larr; Kembali ke Daftar Tiket Saya
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 px-4 py-1.5 rounded cursor-pointer">
                Cetak Tiket Digital (Print / PDF)
            </button>
        </div>
    </div>

    <!-- KARTU TIKET DIGITAL RESMI -->
    <div class="ticket-container bg-white border-2 border-slate-800 rounded shadow-sm overflow-hidden">

        <!-- Header Tiket -->
        <div class="bg-slate-900 text-white p-6 border-b-2 border-slate-800">
            <div class="flex flex-wrap justify-between items-start gap-4">
                <div>
                    <span class="text-xs font-bold text-amber-400 tracking-wider uppercase block mb-1">
                        TIKET MASUK ELEKTRONIK RESMI (E-TICKET)
                    </span>
                    <h1 class="text-2xl font-black tracking-tight">WORKSHOPTIX</h1>
                    <span class="text-xs text-slate-300">Penyelenggara: SMK PESAT Rekayasa Perangkat Lunak</span>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 bg-emerald-600 text-white text-xs font-extrabold tracking-wider rounded border border-emerald-400 uppercase">
                        [STATUS: LUNAS & TERVERIFIKASI]
                    </span>
                    <span class="block text-[11px] text-slate-400 mt-1 font-mono">
                        {{ $registration->paid_at ? $registration->paid_at->translatedFormat('d F Y, H:i') : now()->translatedFormat('d F Y, H:i') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Badan Tiket: Rincian Sesuai LKPD (Kode Tiket, Nama Peserta, Workshop, Jadwal) -->
        <div class="p-6 sm:p-8 space-y-6">

            <!-- Banner Kode Tiket Utama -->
            <div class="p-4 bg-slate-100 border border-slate-300 rounded text-center">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-widest block mb-1">KODE RESMI TIKET DIGITAL</span>
                <span class="font-mono text-2xl sm:text-3xl font-black text-blue-900 tracking-wider">
                    {{ $registration->ticket_code }}
                </span>
                <span class="block text-[10px] text-slate-500 mt-1 uppercase">Tunjukkan kode ini kepada panitia meja registrasi saat hari pelaksanaan</span>
            </div>

            <!-- Tabel Data Tiket -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                
                <div class="space-y-4">
                    <div class="border-b border-slate-200 pb-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">NAMA PESERTA</span>
                        <span class="text-base font-bold text-slate-900 block mt-0.5">{{ $user->name }}</span>
                        <span class="text-[11px] text-slate-500">{{ $user->email }}</span>
                    </div>

                    <div class="border-b border-slate-200 pb-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">JUDUL WORKSHOP IT</span>
                        <span class="text-sm font-bold text-slate-900 block mt-0.5 leading-snug">{{ $workshop->title }}</span>
                        <span class="text-[11px] text-blue-700 font-semibold uppercase">[{{ $workshop->category }}]</span>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">INSTRUKTUR PELATIHAN</span>
                        <span class="font-bold text-slate-800 block mt-0.5">{{ $workshop->instructor_name }}</span>
                        <span class="text-[11px] text-slate-500">{{ $workshop->instructor_title }}</span>
                    </div>
                </div>

                <div class="space-y-4 sm:border-l sm:border-slate-200 sm:pl-6">
                    <div class="border-b border-slate-200 pb-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">HARI & TANGGAL PELAKSANAAN</span>
                        <span class="text-sm font-bold text-slate-900 block mt-0.5">
                            {{ $workshop->schedule_date->translatedFormat('l, d F Y') }}
                        </span>
                        <span class="text-[11px] text-slate-600 font-semibold">
                            Pukul {{ $workshop->schedule_date->format('H:i') }} WIB (Durasi: {{ $workshop->duration_hours }} Jam)
                        </span>
                    </div>

                    <div class="border-b border-slate-200 pb-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">LOKASI / RUANG PELAKSANAAN</span>
                        <span class="font-bold text-slate-800 block mt-0.5">{{ $workshop->location }}</span>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">RINCIAN PEMBAYARAN MIDTRANS</span>
                        <span class="font-semibold text-slate-800 block mt-0.5">Metode: {{ $registration->payment_type ?? 'Midtrans Sandbox' }}</span>
                        <span class="text-[11px] font-mono text-slate-500">Ref: {{ $registration->payment_reference ?? '-' }}</span>
                        <span class="font-bold text-blue-800 block mt-1">Total Lunas: {{ $registration->formatted_amount }}</span>
                    </div>
                </div>

            </div>

            <!-- Blok Validasi Keamanan (Verifikasi Tekstual & Barcode Box Tanpa Aset Gambar Eksternal) -->
            <div class="border-t-2 border-dashed border-slate-300 pt-6 mt-6">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50 border border-slate-200 p-4 rounded text-xs">
                    <div>
                        <span class="font-bold text-slate-900 block mb-1">VALIDASI KEAMANAN DIGITAL</span>
                        <p class="text-[11px] text-slate-600 max-w-sm">
                            Tiket ini sah diterbitkan oleh sistem WORKSHOPTIX SMK PESAT RPL. Setiap tiket hanya berlaku untuk satu orang peserta terdaftar.
                        </p>
                    </div>
                    <div class="text-center font-mono border-2 border-slate-400 px-4 py-2 bg-white rounded">
                        <span class="text-[9px] text-slate-400 block tracking-widest uppercase">KODE KEAMANAN SISTEM</span>
                        <span class="text-sm font-black text-slate-800 tracking-wider">
                            SEC-{{ strtoupper(substr(md5($registration->ticket_code . $registration->user_id), 0, 10)) }}
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer Tiket -->
        <div class="bg-slate-100 border-t border-slate-300 p-4 text-center text-[11px] text-slate-600">
            Harap hadir 15 menit sebelum acara dimulai dan membawa kartu identitas siswa / peserta.
        </div>

    </div>

</div>
@endsection
