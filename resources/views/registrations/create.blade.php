@extends('layouts.app')

@section('title', 'Konfirmasi Pendaftaran - WORKSHOPTIX')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    <div class="mb-6">
        <a href="{{ route('catalog.show', $workshop) }}" class="inline-flex items-center text-xs font-bold text-blue-700 hover:text-blue-900 bg-white border border-slate-300 px-3 py-1.5 rounded">
            &larr; Batalkan dan Kembali ke Detail Workshop
        </a>
    </div>

    <div class="bg-white border border-slate-300 rounded shadow-sm overflow-hidden">
        <div class="bg-slate-900 p-6 text-white border-b border-slate-800">
            <span class="text-xs font-bold text-amber-400 uppercase tracking-wider block mb-1">LANGKAH 1 DARI 2 • FORMULIR PENDAFTARAN</span>
            <h1 class="text-xl font-bold">Konfirmasi Pemesanan Tiket Workshop</h1>
            <p class="text-xs text-slate-300 mt-1">Pastikan data identitas peserta dan topik pelatihan sudah sesuai sebelum memproses transaksi</p>
        </div>

        <form method="POST" action="{{ route('registrations.store', $workshop) }}" class="p-6 space-y-6">
            @csrf

            <!-- Rincian Workshop yang Dipilih -->
            <div class="bg-slate-50 border border-slate-200 p-4 rounded text-xs space-y-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">WORKSHOP TERPILIH</span>
                <div class="text-base font-bold text-slate-900">{{ $workshop->title }}</div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-2 text-slate-600 border-t border-slate-200">
                    <div>
                        <span class="font-bold text-slate-700 block">Jadwal:</span>
                        {{ $workshop->schedule_date->translatedFormat('d F Y, H:i') }} WIB
                    </div>
                    <div>
                        <span class="font-bold text-slate-700 block">Instruktur:</span>
                        {{ $workshop->instructor_name }}
                    </div>
                    <div>
                        <span class="font-bold text-slate-700 block">Lokasi:</span>
                        {{ $workshop->location }}
                    </div>
                </div>
            </div>

            <!-- Identitas Peserta -->
            <div class="border border-slate-200 rounded p-4">
                <span class="text-xs font-bold text-slate-800 uppercase tracking-wider block mb-3">DATA PESERTA TERDAFTAR</span>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div>
                        <span class="text-slate-500 block">Nama Lengkap:</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $user->name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Email Peserta:</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $user->email }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Nomor WhatsApp:</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $user->phone ?? 'Belum diisi' }}</span>
                    </div>
                </div>
            </div>

            <!-- Catatan Tambahan (Opsional) -->
            <div>
                <label for="notes" class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan untuk Panitia (Opsional)</label>
                <textarea name="notes" id="notes" rows="2" placeholder="Tuliskan catatan khusus atau pertanyaan terkait workshop jika ada..."
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">{{ old('notes') }}</textarea>
            </div>

            <!-- Ringkasan Biaya -->
            <div class="bg-blue-50 border border-blue-200 p-4 rounded flex justify-between items-center">
                <div>
                    <span class="text-xs font-bold text-blue-950 uppercase block">TOTAL TAGIHAN (DITETAPKAN OLEH SERVER):</span>
                    <span class="text-[11px] text-blue-800">Nominal resmi terproteksi dari modifikasi client-side</span>
                </div>
                <div class="text-2xl font-black text-blue-900">
                    {{ $workshop->formatted_price }}
                </div>
            </div>

            <!-- Persetujuan Ketentuan & Anti Pendaftaran Ganda -->
            <div class="p-3 bg-slate-50 border border-slate-200 rounded text-xs">
                <label class="flex items-start gap-2 cursor-pointer">
                    <input type="checkbox" name="agreement" value="1" required class="mt-0.5 rounded border-slate-300 text-blue-600">
                    <span class="text-slate-700">
                        Saya mengonfirmasi bahwa data yang tertera adalah benar dan menyetujui ketentuan pendaftaran. Saya memahami bahwa sistem tidak menerima pendaftaran ganda pada workshop yang sama.
                    </span>
                </label>
            </div>

            <!-- Tombol Submit -->
            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 bg-blue-700 hover:bg-blue-800 text-white font-bold text-sm rounded cursor-pointer transition">
                    Lanjutkan ke Pembayaran (Midtrans Sandbox) &rarr;
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
