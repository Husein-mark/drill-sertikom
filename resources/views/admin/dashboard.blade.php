@extends('layouts.app')

@section('title', 'Dashboard Panitia - WORKSHOPTIX')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header Dashboard Panitia -->
    <div class="bg-slate-900 text-white p-6 rounded border border-slate-800 mb-8 flex flex-wrap justify-between items-center gap-4">
        <div>
            <span class="text-xs font-bold text-amber-400 uppercase tracking-wider block mb-1">PANEL KENDALI ADMINISTRATOR WORKSHOP</span>
            <h1 class="text-2xl font-black">Dashboard Panitia Penyelenggara</h1>
            <p class="text-xs text-slate-300 mt-1">Kelola data workshop, pantau status pembayaran peserta, dan lakukan verifikasi</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.workshops.create') }}" class="py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded transition">
                + Tambah Workshop Baru
            </a>
            <a href="{{ route('admin.registrations.index') }}" class="py-2 px-4 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded transition">
                Daftar Pendaftar & Uji N+1
            </a>
        </div>
    </div>

    <!-- Metrik Statistik Utama -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        
        <div class="bg-white border border-slate-300 rounded p-5">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">TOTAL WORKSHOP AKTIF</span>
            <div class="text-3xl font-black text-slate-900">{{ $totalWorkshops }}</div>
            <span class="text-xs text-slate-500 mt-1 block">Kelas dibuka untuk pendaftaran</span>
        </div>

        <div class="bg-white border border-slate-300 rounded p-5">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">TOTAL TRANSAKSI PENDAFTAR</span>
            <div class="text-3xl font-black text-blue-800">{{ $totalRegistrations }}</div>
            <span class="text-xs text-slate-500 mt-1 block">{{ $totalPending }} masih menunggu pembayaran</span>
        </div>

        <div class="bg-white border border-slate-300 rounded p-5">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">TIKET LUNAS / TERBIT</span>
            <div class="text-3xl font-black text-emerald-700">{{ $totalPaid }}</div>
            <span class="text-xs text-emerald-800 mt-1 block">Telah diverifikasi server</span>
        </div>

        <div class="bg-white border border-slate-300 rounded p-5">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">TOTAL REVENUE (LUNAS)</span>
            <div class="text-2xl font-black text-blue-900">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
            <span class="text-xs text-slate-500 mt-1 block">Dari transaksi Midtrans berhasil</span>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Tabel Pendaftar Terbaru -->
        <div class="lg:col-span-8 bg-white border border-slate-300 rounded shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Transaksi Pendaftaran Terkini</h2>
                    <span class="text-xs text-slate-500">6 transaksi pendaftaran paling akhir</span>
                </div>
                <a href="{{ route('admin.registrations.index') }}" class="text-xs font-bold text-blue-700 hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 text-slate-700 uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">No. Registrasi</th>
                            <th class="py-2.5 px-3">Nama Peserta</th>
                            <th class="py-2.5 px-3">Workshop</th>
                            <th class="py-2.5 px-3 text-center">Status</th>
                            <th class="py-2.5 px-3 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentRegistrations as $reg)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-3 font-mono font-bold text-slate-800">{{ $reg->registration_number }}</td>
                                <td class="py-3 px-3">
                                    <span class="font-bold text-slate-900 block">{{ $reg->user->name }}</span>
                                    <span class="text-[10px] text-slate-500">{{ $reg->user->email }}</span>
                                </td>
                                <td class="py-3 px-3 text-slate-700 truncate max-w-[200px]">{{ $reg->workshop->title }}</td>
                                <td class="py-3 px-3 text-center">
                                    @if($reg->isPaid())
                                        <span class="inline-block px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold text-[10px] border border-emerald-300">
                                            [LUNAS]
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-bold text-[10px] border border-amber-300">
                                            [PENDING]
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right font-bold text-slate-800">{{ $reg->formatted_amount }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-500">Belum ada transaksi pendaftaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Kolom Kanan: Kuota Workshop & Quick Actions -->
        <div class="lg:col-span-4 space-y-6">
            
            <div class="bg-white border border-slate-300 rounded p-5">
                <span class="text-xs font-bold text-slate-900 uppercase tracking-wider block mb-3">KAPASITAS WORKSHOP</span>
                <div class="space-y-4 text-xs">
                    @foreach($workshops as $ws)
                        <div class="border-b border-slate-100 pb-2">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-bold text-slate-800 truncate max-w-[180px]">{{ $ws->title }}</span>
                                <span class="font-mono text-slate-600">{{ $ws->paid_count }} / {{ $ws->quota }}</span>
                            </div>
                            <div class="w-full bg-slate-200 h-2 rounded overflow-hidden">
                                @php
                                    $pct = $ws->quota > 0 ? min(100, round(($ws->paid_count / $ws->quota) * 100)) : 0;
                                @endphp
                                <div class="bg-blue-700 h-2" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="text-[10px] text-slate-500 block mt-1">Terisi {{ $pct }}% kapasitas</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-2 text-center">
                    <a href="{{ route('admin.workshops.index') }}" class="text-xs font-bold text-blue-700 hover:underline">
                        Buka Manajemen Workshop &rarr;
                    </a>
                </div>
            </div>

            <!-- Petunjuk Pengujian Sertifikasi -->
            <div class="bg-slate-50 border border-slate-300 rounded p-4 text-xs space-y-2">
                <span class="font-bold text-slate-900 block">[PETUNJUK DRILL PRA SERTIFIKASI]</span>
                <p class="text-slate-600 leading-relaxed">
                    Gunakan menu <strong>Pendaftar & Uji N+1</strong> untuk menunjukkan kepada penguji sertifikasi perbandingan performa query sebelum dan sesudah <em>Eager Loading</em>.
                </p>
            </div>

        </div>

    </div>

</div>
@endsection
