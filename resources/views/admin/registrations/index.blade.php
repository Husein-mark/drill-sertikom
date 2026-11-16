@extends('layouts.app')

@section('title', 'Daftar Pendaftar & Uji N+1 Query - WORKSHOPTIX')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6 flex flex-wrap justify-between items-center gap-4">
        <div>
            <span class="text-xs font-bold text-amber-600 uppercase tracking-wider block">LANGKAH 05 LKPD • DRILL PRA SERTIFIKASI</span>
            <h1 class="text-2xl font-bold text-slate-900">Daftar Pendaftar & Analisis Query Database</h1>
            <p class="text-xs text-slate-500 mt-1">Uji perbandingan jumlah query SQL antara mode Eager Loading dan N+1 Problem secara nyata</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="py-2 px-3 text-xs font-bold text-slate-700 bg-white border border-slate-300 rounded hover:bg-slate-50">
                &larr; Dashboard
            </a>
            <a href="{{ route('admin.workshops.index') }}" class="py-2 px-3 text-xs font-bold text-blue-700 bg-white border border-slate-300 rounded hover:bg-slate-50">
                Kelola Workshop
            </a>
        </div>
    </div>

    <!-- PANEL UTAMA PEMBUKTIAN LKPD 05: UJI KOMPARASI N+1 VS EAGER LOADING -->
    <div class="bg-white border-2 {{ $mode === 'eager' ? 'border-emerald-600' : 'border-rose-600' }} rounded shadow-sm p-6 mb-8">
        <div class="flex flex-wrap justify-between items-start gap-4 mb-4 border-b border-slate-200 pb-4">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider block {{ $mode === 'eager' ? 'text-emerald-700' : 'text-rose-700' }}">
                    INSTRUMEN EVALUASI KOMPETENSI DATABASE LARAVEL
                </span>
                <h2 class="text-lg font-black text-slate-900">
                    Status Pengujian: {{ $mode === 'eager' ? 'DENGAN EAGER LOADING (OPTIMAL)' : 'TANPA EAGER LOADING (N+1 QUERY PROBLEM)' }}
                </h2>
            </div>

            <!-- Tombol Pengalih Mode Pengujian -->
            <div class="flex items-center gap-2">
                <a href="{{ request()->fullUrlWithQuery(['mode' => 'eager']) }}"
                    class="py-2 px-3.5 text-xs font-bold rounded cursor-pointer transition {{ $mode === 'eager' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300' }}">
                    [Mode Eager Loading: with('user', 'workshop')]
                </a>
                <a href="{{ request()->fullUrlWithQuery(['mode' => 'lazy']) }}"
                    class="py-2 px-3.5 text-xs font-bold rounded cursor-pointer transition {{ $mode === 'lazy' ? 'bg-rose-700 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300' }}">
                    [Mode Simulasi N+1 Problem]
                </a>
            </div>
        </div>

        <!-- Kartu Metrik Hasil Analisis Query -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4 text-xs">
            <div class="p-4 bg-slate-50 border border-slate-200 rounded">
                <span class="text-[10px] font-bold text-slate-500 uppercase block">JUMLAH QUERY DIEKSEKUSI</span>
                <div class="text-2xl font-black mt-1 {{ $mode === 'eager' ? 'text-emerald-700' : 'text-rose-700' }}">
                    {{ $queryCount }} Query SQL
                </div>
                <span class="text-[11px] font-semibold text-slate-600 block mt-0.5">
                    {{ $mode === 'eager' ? 'Efisien: Data relasi diambil sekaligus' : 'Boros: 1 query induk + N query relasi' }}
                </span>
            </div>

            <div class="p-4 bg-slate-50 border border-slate-200 rounded">
                <span class="text-[10px] font-bold text-slate-500 uppercase block">WAKTU EKSEKUSI DATABASE</span>
                <div class="text-2xl font-black text-slate-900 mt-1">
                    {{ $executionTime }} ms
                </div>
                <span class="text-[11px] font-semibold text-slate-600 block mt-0.5">
                    Total durasi waktu pemrosesan query
                </span>
            </div>

            <div class="p-4 bg-slate-50 border border-slate-200 rounded">
                <span class="text-[10px] font-bold text-slate-500 uppercase block">TOTAL DATA PENDAFTAR</span>
                <div class="text-2xl font-black text-blue-900 mt-1">
                    {{ $registrations->count() }} Baris Data
                </div>
                <span class="text-[11px] font-semibold text-slate-600 block mt-0.5">
                    Data yang diproses dalam tabel di bawah
                </span>
            </div>
        </div>

        <!-- Penjelasan Ilmiah untuk Uji Sertifikasi -->
        <div class="p-3 bg-slate-100 border border-slate-300 rounded text-xs text-slate-700 leading-relaxed mb-4">
            <span class="font-bold block mb-1">ANALISIS TEKNIS UNTUK LEMBAR JAWABAN LKPD:</span>
            @if($mode === 'eager')
                <p>
                    Saat menggunakan <code>Registration::with(['user', 'workshop'])</code>, Laravel mengeksekusi <strong>hanya 3 query terpisah</strong> terlepas dari berapapun jumlah baris pendaftar: 1 query untuk tabel <code>registrations</code>, 1 query untuk seluruh <code>users</code> terkait dengan <code>WHERE id IN (...)</code>, dan 1 query untuk <code>workshops</code> terkait. Hal ini mencegah bottleneck pada server basis data (Zero N+1 Problem).
                </p>
            @else
                <p class="text-rose-900">
                    Pada mode tanpa Eager Loading, sistem pertama-tama mengambil data <code>registrations</code> (1 query). Kemudian saat view menampilkan <code>$item->user->name</code> dan <code>$item->workshop->title</code>, Laravel terpaksa mengirim query SQL baru <strong>secara berulang-ulang untuk setiap baris data</strong>. Untuk {{ $registrations->count() }} data pendaftar, terjadi {{ $queryCount }} kali lalu-lintas query ke database. Ini adalah definisi klasik dari <strong>N+1 Query Problem</strong>.
                </p>
            @endif
        </div>

        <!-- Detail Daftar Query SQL yang Dijalankan (Query Log) -->
        <details class="text-xs border border-slate-300 rounded bg-white p-3 cursor-pointer">
            <summary class="font-bold text-slate-800">
                Klik untuk Melihat Rincian {{ $queryCount }} Query SQL yang Dieksekusi Sistem
            </summary>
            <div class="mt-3 space-y-2 max-h-72 overflow-y-auto pt-2 border-t border-slate-200">
                @foreach($queries as $index => $q)
                    <div class="p-2 bg-slate-50 rounded border border-slate-200 font-mono text-[11px]">
                        <div class="flex justify-between items-center text-slate-500 text-[10px] mb-1">
                            <span>#Query {{ $index + 1 }}</span>
                            <span class="font-bold text-blue-700">{{ $q['time'] }} ms</span>
                        </div>
                        <div class="text-slate-900 break-all">
                            {{ $q['query'] }}
                        </div>
                        @if(!empty($q['bindings']))
                            <div class="text-slate-500 text-[10px] mt-1">
                                Bindings: [{{ implode(', ', array_map(fn($b) => is_numeric($b) ? $b : "'{$b}'", $q['bindings'])) }}]
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </details>
    </div>

    <!-- Filter Pendaftar -->
    <div class="bg-white border border-slate-200 rounded p-4 mb-6">
        <form method="GET" action="{{ route('admin.registrations.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end text-xs">
            <input type="hidden" name="mode" value="{{ $mode }}">

            <div class="sm:col-span-5">
                <label for="workshop_id" class="block font-bold text-slate-700 mb-1">Filter Workshop</label>
                <select name="workshop_id" id="workshop_id" class="w-full px-3 py-2 text-xs border border-slate-300 rounded bg-white">
                    <option value="">Semua Workshop IT</option>
                    @foreach($workshops as $ws)
                        <option value="{{ $ws->id }}" {{ $workshopId == $ws->id ? 'selected' : '' }}>
                            {{ $ws->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-4">
                <label for="status" class="block font-bold text-slate-700 mb-1">Status Pembayaran</label>
                <select name="status" id="status" class="w-full px-3 py-2 text-xs border border-slate-300 rounded bg-white">
                    <option value="">Semua Status</option>
                    <option value="paid" {{ $statusFilter === 'paid' ? 'selected' : '' }}>Lunas / Tiket Terbit</option>
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="cancelled" {{ $statusFilter === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div class="sm:col-span-3 flex gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded">
                    Terapkan
                </button>
                @if($workshopId || $statusFilter)
                    <a href="{{ route('admin.registrations.index', ['mode' => $mode]) }}" class="py-2 px-3 bg-slate-200 text-slate-700 font-bold rounded text-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Pendaftar Sesuai Poin 05 LKPD -->
    <div class="bg-white border border-slate-300 rounded shadow-sm overflow-hidden">
        <div class="p-4 bg-slate-900 text-white flex justify-between items-center text-xs">
            <span class="font-bold uppercase tracking-wider">TABEL DATA PENDAFTAR WORKSHOP IT</span>
            <span class="font-mono text-slate-300">Total: {{ $registrations->count() }} Data</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 text-slate-700 uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">No. Registrasi</th>
                        <th class="py-3 px-4">Nama Peserta</th>
                        <th class="py-3 px-4">Judul Workshop</th>
                        <th class="py-3 px-4">Nominal</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4">Kode Tiket</th>
                        <th class="py-3 px-4 text-right">Aksi Panitia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($registrations as $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-800">
                                {{ $item->registration_number }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 block">{{ $item->user->name ?? 'User Terhapus' }}</span>
                                <span class="text-[11px] text-slate-500">{{ $item->user->email ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-800 block leading-snug">{{ $item->workshop->title ?? 'Workshop Terhapus' }}</span>
                                <span class="text-[10px] text-blue-700 font-bold uppercase">[{{ $item->workshop->category ?? '-' }}]</span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-blue-900">
                                {{ $item->formatted_amount }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($item->isPaid())
                                    <span class="inline-block px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold text-[10px] border border-emerald-300">
                                        [LUNAS]
                                    </span>
                                @elseif($item->status === 'pending')
                                    <span class="inline-block px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-bold text-[10px] border border-amber-300">
                                        [PENDING]
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded bg-rose-100 text-rose-800 font-bold text-[10px] border border-rose-300">
                                        [{{ strtoupper($item->status) }}]
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono text-[11px]">
                                @if($item->ticket_code)
                                    <span class="font-bold text-emerald-800">{{ $item->ticket_code }}</span>
                                @else
                                    <span class="text-slate-400 italic">Belum terbit</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($item->isPaid())
                                        <a href="{{ route('registrations.ticket', $item) }}" target="_blank" class="py-1 px-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-[11px] rounded">
                                            Buka Tiket
                                        </a>
                                    @elseif($item->status === 'pending')
                                        <form method="POST" action="{{ route('admin.registrations.verify', $item) }}">
                                            @csrf
                                            <button type="submit" class="py-1 px-2.5 bg-blue-700 hover:bg-blue-800 text-white font-bold text-[11px] rounded cursor-pointer">
                                                Verifikasi Manual
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.registrations.cancel', $item) }}" onsubmit="return confirm('Batalkan pendaftaran ini?')">
                                            @csrf
                                            <button type="submit" class="py-1 px-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-[11px] rounded cursor-pointer">
                                                Batal
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">
                                Tidak ada data pendaftar yang cocok dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
