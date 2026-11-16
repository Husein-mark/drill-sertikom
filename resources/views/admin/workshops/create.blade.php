@extends('layouts.app')

@section('title', 'Tambah Workshop Baru - WORKSHOPTIX')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    <div class="mb-6">
        <a href="{{ route('admin.workshops.index') }}" class="inline-flex items-center text-xs font-bold text-blue-700 hover:text-blue-900 bg-white border border-slate-300 px-3 py-1.5 rounded">
            &larr; Kembali ke Daftar Workshop
        </a>
    </div>

    <div class="bg-white border border-slate-300 rounded shadow-sm overflow-hidden">
        <div class="bg-slate-900 text-white p-6 border-b border-slate-800">
            <span class="text-xs font-bold text-amber-400 uppercase tracking-wider block mb-1">MANAJEMEN KONTEN PELATIHAN</span>
            <h1 class="text-xl font-bold">Tambah Workshop IT Baru</h1>
            <p class="text-xs text-slate-300 mt-1">Lengkapi data workshop untuk membuka pendaftaran tiket bagi peserta</p>
        </div>

        <form method="POST" action="{{ route('admin.workshops.store') }}" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Judul -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-xs font-bold text-slate-700 mb-1">Judul Workshop IT *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        placeholder="Contoh: Optimasi Arsitektur Database dan RESTful API di Laravel"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
                </div>

                <!-- Kategori -->
                <div>
                    <label for="category" class="block text-xs font-bold text-slate-700 mb-1">Kategori Bidang IT *</label>
                    <input type="text" name="category" id="category" value="{{ old('category') }}" required
                        placeholder="Contoh: Web Development / Cyber Security"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
                </div>

                <!-- Lokasi -->
                <div>
                    <label for="location" class="block text-xs font-bold text-slate-700 mb-1">Lokasi / Ruang Pelaksanaan *</label>
                    <input type="text" name="location" id="location" value="{{ old('location', 'Lab Komputer RPL SMK PESAT') }}" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
                </div>

                <!-- Nama Instruktur -->
                <div>
                    <label for="instructor_name" class="block text-xs font-bold text-slate-700 mb-1">Nama Instruktur / Pemateri *</label>
                    <input type="text" name="instructor_name" id="instructor_name" value="{{ old('instructor_name') }}" required
                        placeholder="Contoh: Ahmad Fauzi, S.Kom."
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
                </div>

                <!-- Jabatan Instruktur -->
                <div>
                    <label for="instructor_title" class="block text-xs font-bold text-slate-700 mb-1">Jabatan / Gelar Profesional Instruktur</label>
                    <input type="text" name="instructor_title" id="instructor_title" value="{{ old('instructor_title') }}"
                        placeholder="Contoh: Senior Software Engineer di PT Inovasi IT"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
                </div>

                <!-- Jadwal Tanggal & Waktu -->
                <div>
                    <label for="schedule_date" class="block text-xs font-bold text-slate-700 mb-1">Jadwal Tanggal & Jam Pelaksanaan *</label>
                    <input type="datetime-local" name="schedule_date" id="schedule_date" value="{{ old('schedule_date') }}" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
                </div>

                <!-- Durasi (Jam) -->
                <div>
                    <label for="duration_hours" class="block text-xs font-bold text-slate-700 mb-1">Durasi Pelatihan (Jam) *</label>
                    <input type="number" name="duration_hours" id="duration_hours" value="{{ old('duration_hours', 3) }}" min="1" max="12" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
                </div>

                <!-- Biaya Investasi -->
                <div>
                    <label for="price" class="block text-xs font-bold text-slate-700 mb-1">Biaya Tiket Resmi (Rupiah) *</label>
                    <input type="number" name="price" id="price" value="{{ old('price', 50000) }}" min="0" step="1000" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
                    <span class="text-[11px] text-slate-500 block mt-1">Masukkan 0 jika workshop bersifat gratis</span>
                </div>

                <!-- Kuota Kursi -->
                <div>
                    <label for="quota" class="block text-xs font-bold text-slate-700 mb-1">Kapasitas Kuota Peserta *</label>
                    <input type="number" name="quota" id="quota" value="{{ old('quota', 40) }}" min="1" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
                </div>

                <!-- Status Aktif -->
                <div class="md:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                        <span class="text-xs font-bold text-slate-800">Buka workshop ini langsung untuk publik di katalog</span>
                    </label>
                </div>

                <!-- Deskripsi & Silabus -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-xs font-bold text-slate-700 mb-1">Deskripsi & Silabus Pembelajaran *</label>
                    <textarea name="description" id="description" rows="5" required
                        placeholder="Uraikan materi, prasyarat, dan target kompetensi workshop ini..."
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="border-t border-slate-200 pt-4 flex justify-end gap-3">
                <a href="{{ route('admin.workshops.index') }}" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded border border-slate-300">
                    Batal
                </a>
                <button type="submit" class="py-2.5 px-6 bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs rounded cursor-pointer transition">
                    Simpan & Publikasikan Workshop
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
