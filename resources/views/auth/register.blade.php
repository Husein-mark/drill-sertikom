@extends('layouts.app')

@section('title', 'Daftar Akun Baru - WORKSHOPTIX')

@section('content')
<div class="max-w-md mx-auto my-10 px-4">
    <div class="bg-white border border-slate-300 rounded shadow-sm p-8">
        <div class="border-b border-slate-200 pb-4 mb-6 text-center">
            <span class="text-xs font-bold text-blue-700 tracking-wider uppercase block mb-1">PENDAFTARAN PENGGUNA</span>
            <h1 class="text-2xl font-bold text-slate-900">Buat Akun Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Daftarkan akun untuk memesan tiket workshop dan sertifikasi IT</p>
        </div>

        <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Siswa / Peserta</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                    placeholder="Contoh: Muhammad Fikri Pratama"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Alamat Email Aktif</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    placeholder="nama@email.com"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
            </div>

            <div>
                <label for="phone" class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                    placeholder="Contoh: 081234567890"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
            </div>

            <div>
                <label for="role" class="block text-xs font-bold text-slate-700 mb-1">Peran Akun</label>
                <select name="role" id="role" class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
                    <option value="peserta" {{ old('role') === 'peserta' ? 'selected' : '' }}>Peserta Workshop</option>
                    <option value="panitia" {{ old('role') === 'panitia' ? 'selected' : '' }}>Panitia Penyelenggara</option>
                </select>
                <span class="text-[11px] text-slate-500 block mt-1">Pilih peran akun sesuai kebutuhan pengujian LKPD</span>
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi (Minimal 6 Karakter)</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">Ulangi Kata Sandi</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-blue-700 hover:bg-blue-800 text-white font-bold text-sm rounded cursor-pointer transition">
                    Selesaikan Pendaftaran
                </button>
            </div>
        </form>

        <div class="mt-6 pt-4 border-t border-slate-200 text-center text-xs text-slate-500">
            Sudah memiliki akun terdaftar? <a href="{{ route('login') }}" class="text-blue-700 font-bold hover:underline">Masuk di sini</a>
        </div>
    </div>
</div>
@endsection
