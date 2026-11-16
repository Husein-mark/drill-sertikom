@extends('layouts.app')

@section('title', 'Katalog Workshop IT - WORKSHOPTIX')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Banner Utama Sesuai Brief LKPD -->
    <div class="bg-slate-900 border border-slate-800 rounded p-6 sm:p-8 text-white mb-8">
        <div class="max-w-3xl">
            <div class="inline-block px-3 py-1 bg-amber-500 text-slate-950 font-extrabold text-xs tracking-wider rounded mb-3">
                DRILL PRA SERTIFIKASI RPL • TIKET DIGITAL
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-3">
                Pendaftaran & Tiket Digital Workshop IT
            </h1>
            <p class="text-sm text-slate-300 leading-relaxed mb-4">
                Panitia sekolah menyelenggarakan beberapa workshop IT berbayar. Pilih topik workshop yang Anda minati, lakukan pendaftaran, selesaikan transaksi simulasi Midtrans Sandbox, dan dapatkan tiket digital resmi setelah pembayaran terverifikasi oleh server.
            </p>
            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400 border-t border-slate-800 pt-3">
                <span class="font-bold text-slate-200">STANDAR ALUR:</span>
                <span>1. Pilih Workshop</span>
                <span class="text-slate-600">&rarr;</span>
                <span>2. Daftar Akun / Form</span>
                <span class="text-slate-600">&rarr;</span>
                <span>3. Bayar Sandbox</span>
                <span class="text-slate-600">&rarr;</span>
                <span>4. Verifikasi Backend</span>
                <span class="text-slate-600">&rarr;</span>
                <span>5. Terbit Tiket Resmi</span>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian Workshop -->
    <div class="bg-white border border-slate-200 rounded p-4 mb-8">
        <form method="GET" action="{{ route('catalog.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-5">
                <label for="search" class="block text-xs font-bold text-slate-700 mb-1">Cari Judul atau Pemateri</label>
                <input type="text" name="search" id="search" value="{{ $search }}"
                    placeholder="Ketik kata kunci workshop..."
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
            </div>

            <div class="md:col-span-4">
                <label for="category" class="block text-xs font-bold text-slate-700 mb-1">Kategori Bidang IT</label>
                <select name="category" id="category" class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
                    <option value="">Semua Kategori Bidang</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $selectedCategory === $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3 flex gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs rounded cursor-pointer">
                    Terapkan Filter
                </button>
                @if($search || $selectedCategory)
                    <a href="{{ route('catalog.index') }}" class="py-2 px-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded text-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Daftar Kartu Workshop -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Daftar Pilihan Workshop Tersedia</h2>
            <p class="text-xs text-slate-500">Menampilkan {{ $workshops->total() }} workshop terdaftar dalam sistem</p>
        </div>
    </div>

    @if($workshops->isEmpty())
        <div class="bg-white border border-slate-300 rounded p-12 text-center my-6">
            <span class="block text-sm font-bold text-slate-700 mb-1">[TIDAK ADA DATA WORKSHOP DITEMUKAN]</span>
            <p class="text-xs text-slate-500 max-w-md mx-auto mb-4">
                Tidak ada workshop yang cocok dengan kriteria pencarian Anda. Silakan coba kata kunci lain atau reset filter.
            </p>
            <a href="{{ route('catalog.index') }}" class="inline-block py-2 px-4 bg-blue-700 text-white text-xs font-bold rounded">
                Lihat Semua Workshop
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($workshops as $workshop)
                <div class="bg-white border border-slate-300 rounded flex flex-col justify-between hover:border-slate-400 transition">
                    <!-- Bagian Atas Kartu -->
                    <div class="p-5">
                        <div class="flex justify-between items-start gap-2 mb-3">
                            <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-blue-50 text-blue-800 border border-blue-200">
                                [{{ $workshop->category }}]
                            </span>
                            @if($workshop->available_quota <= 0)
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-rose-50 text-rose-800 border border-rose-300">
                                    [KUOTA PENUH]
                                </span>
                            @else
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-300">
                                    [SISA: {{ $workshop->available_quota }} KURSI]
                                </span>
                            @endif
                        </div>

                        <h3 class="text-base font-bold text-slate-900 mb-2 leading-snug">
                            <a href="{{ route('catalog.show', $workshop) }}" class="hover:text-blue-700">
                                {{ $workshop->title }}
                            </a>
                        </h3>

                        <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed mb-4">
                            {{ $workshop->description }}
                        </p>

                        <!-- Rincian Pemateri & Jadwal -->
                        <div class="border-t border-slate-100 pt-3 space-y-2 text-xs">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">PEMATERI:</span>
                                <span class="font-bold text-slate-800">{{ $workshop->instructor_name }}</span>
                                <span class="text-[11px] text-slate-500 truncate">{{ $workshop->instructor_title }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-1 border-t border-slate-100">
                                <span class="text-slate-500">Jadwal:</span>
                                <span class="font-semibold text-slate-800">{{ $workshop->schedule_date->translatedFormat('d F Y, H:i') }} WIB</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Lokasi:</span>
                                <span class="text-slate-700 truncate max-w-[180px]">{{ $workshop->location }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bagian Bawah Kartu: Harga & Tombol -->
                    <div class="bg-slate-50 border-t border-slate-200 p-4 flex items-center justify-between">
                        <div>
                            <span class="block text-[10px] font-bold text-slate-500 uppercase">INVESTASI:</span>
                            <span class="text-base font-black text-blue-800">{{ $workshop->formatted_price }}</span>
                        </div>
                        <a href="{{ route('catalog.show', $workshop) }}" class="py-2 px-3 text-xs font-bold text-white bg-blue-700 hover:bg-blue-800 rounded transition">
                            Detail & Daftar
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $workshops->links() }}
        </div>
    @endif
</div>
@endsection
