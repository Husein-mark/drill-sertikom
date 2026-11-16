@extends('layouts.app')

@section('title', 'Kelola Workshop - WORKSHOPTIX')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6 flex flex-wrap justify-between items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Manajemen Workshop IT</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola data seluruh workshop, atur kuota kursi, jadwal, dan biaya tiket</p>
        </div>
        <a href="{{ route('admin.workshops.create') }}" class="py-2.5 px-4 bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs rounded shadow-sm">
            + Tambah Workshop Baru
        </a>
    </div>

    <!-- Search Form -->
    <div class="bg-white border border-slate-200 rounded p-4 mb-6">
        <form method="GET" action="{{ route('admin.workshops.index') }}" class="flex gap-3">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul workshop, pemateri, kategori..."
                class="flex-1 px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
            <button type="submit" class="py-2 px-4 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded cursor-pointer">
                Cari Data
            </button>
            @if($search)
                <a href="{{ route('admin.workshops.index') }}" class="py-2 px-3 bg-slate-200 text-slate-700 font-bold text-xs rounded text-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="bg-white border border-slate-300 rounded shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-900 text-white uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Judul & Kategori</th>
                        <th class="py-3 px-4">Instruktur</th>
                        <th class="py-3 px-4">Jadwal & Lokasi</th>
                        <th class="py-3 px-4">Biaya</th>
                        <th class="py-3 px-4 text-center">Kuota & Pendaftar</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi Kelola</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($workshops as $ws)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-4">
                                <span class="font-bold text-slate-900 text-sm block leading-snug">{{ $ws->title }}</span>
                                <span class="inline-block mt-1 text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200 uppercase">
                                    [{{ $ws->category }}]
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-bold text-slate-800 block">{{ $ws->instructor_name }}</span>
                                <span class="text-[11px] text-slate-500">{{ $ws->instructor_title }}</span>
                            </td>
                            <td class="py-4 px-4 text-slate-700">
                                <span class="font-semibold block">{{ $ws->schedule_date->translatedFormat('d M Y, H:i') }} WIB</span>
                                <span class="text-[11px] text-slate-500 truncate block max-w-[150px]">{{ $ws->location }}</span>
                            </td>
                            <td class="py-4 px-4 font-bold text-blue-900">
                                {{ $ws->formatted_price }}
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="font-bold text-slate-900 block">{{ $ws->registrations_count }} Terdaftar</span>
                                <span class="text-[11px] text-slate-500">Kapasitas: {{ $ws->quota }} Kursi</span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                @if($ws->is_active)
                                    <span class="inline-block px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold text-[10px] border border-emerald-300">
                                        [AKTIF]
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-bold text-[10px]">
                                        [NON-AKTIF]
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.workshops.edit', $ws) }}" class="py-1 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded border border-slate-300">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.workshops.destroy', $ws) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus workshop ini? Data yang sudah memiliki pendaftar tidak dapat dihapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="py-1 px-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded border border-rose-300 cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">
                                Tidak ada data workshop ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200">
            {{ $workshops->links() }}
        </div>
    </div>

</div>
@endsection
