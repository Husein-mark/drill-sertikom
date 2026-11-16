@extends('layouts.app')

@section('title', 'Masuk Akun - WORKSHOPTIX')

@section('content')
<div class="max-w-md mx-auto my-12 px-4">
    <div class="bg-white border border-slate-300 rounded shadow-sm p-8">
        <div class="border-b border-slate-200 pb-4 mb-6 text-center">
            <span class="text-xs font-bold text-blue-700 tracking-wider uppercase block mb-1">AUTENTIKASI SISTEM</span>
            <h1 class="text-2xl font-bold text-slate-900">Masuk ke WORKSHOPTIX</h1>
            <p class="text-xs text-slate-500 mt-1">Masukkan kredensial akun Anda untuk melanjutkan</p>
        </div>

        <!-- Akun Demo Cepat untuk Uji Coba Penguji / Siswa -->
        <div class="mb-6 bg-slate-50 border border-slate-200 p-3 rounded text-xs">
            <span class="font-bold text-slate-800 block mb-1">AKUN UJI COBA (DRILL SERTIKOM):</span>
            <div class="grid grid-cols-2 gap-2 mt-2">
                <button type="button" onclick="fillCredentials('panitia@workshoptix.com', 'password')" class="text-left bg-white border border-slate-300 p-2 rounded hover:bg-slate-100 cursor-pointer">
                    <span class="font-bold text-amber-700 block">[PANITIA]</span>
                    <span class="text-[11px] text-slate-600 truncate block">panitia@workshoptix.com</span>
                </button>
                <button type="button" onclick="fillCredentials('peserta@workshoptix.com', 'password')" class="text-left bg-white border border-slate-300 p-2 rounded hover:bg-slate-100 cursor-pointer">
                    <span class="font-bold text-blue-700 block">[PESERTA]</span>
                    <span class="text-[11px] text-slate-600 truncate block">peserta@workshoptix.com</span>
                </button>
            </div>
        </div>

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded focus:outline-none focus:border-blue-600 bg-white">
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600">
                    <span class="text-slate-600">Ingat sesi saya</span>
                </label>
                <a href="{{ route('register') }}" class="text-blue-700 font-semibold hover:underline">Belum punya akun?</a>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-blue-700 hover:bg-blue-800 text-white font-bold text-sm rounded cursor-pointer transition">
                    Masuk Sekarang
                </button>
            </div>
        </form>

        <div class="mt-6 pt-4 border-t border-slate-200 text-center text-xs text-slate-500">
            Akses pendaftaran workshop dan tiket digital SMK PESAT RPL
        </div>
    </div>
</div>

@push('scripts')
<script>
    function fillCredentials(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endpush
@endsection
