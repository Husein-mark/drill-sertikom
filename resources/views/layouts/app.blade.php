<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WORKSHOPTIX - Tiket Digital Workshop IT')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen flex flex-col font-sans antialiased">

    <!-- Bar Informasi Header -->
    <header class="bg-slate-900 text-slate-200 border-b border-slate-800 text-xs py-2 px-4">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center gap-3">
                <span class="font-bold tracking-wider text-amber-400">DRILL PRA SERTIFIKASI XI RPL</span>
                <span class="text-slate-500">|</span>
                <span>SMK PESAT - Rekayasa Perangkat Lunak</span>
            </div>
            <div class="flex items-center gap-4 text-slate-300">
                <span>Modul Pendaftaran & Pembayaran Digital</span>
                <span class="text-slate-500">|</span>
                <span>Status Server: Aktif</span>
            </div>
        </div>
    </header>

    <!-- Navigasi Utama -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('catalog.index') }}" class="flex items-center gap-2">
                        <span class="text-xl font-extrabold tracking-tight text-blue-800">WORKSHOP<span class="text-amber-600">TIX</span></span>
                        <span class="hidden md:inline-block text-[11px] font-semibold bg-slate-100 text-slate-700 px-2 py-0.5 border border-slate-300 rounded">
                            PORTAL RESMI
                        </span>
                    </a>

                    <!-- Link Menu Publik / Peserta -->
                    <div class="hidden sm:flex sm:items-center sm:gap-2">
                        <a href="{{ route('catalog.index') }}" class="px-3 py-2 text-sm font-semibold rounded {{ request()->routeIs('catalog.*') ? 'text-blue-700 bg-blue-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            Katalog Workshop
                        </a>
                        @auth
                            @if(auth()->user()->isPeserta())
                                <a href="{{ route('registrations.index') }}" class="px-3 py-2 text-sm font-semibold rounded {{ request()->routeIs('registrations.*') ? 'text-blue-700 bg-blue-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                    Tiket Saya
                                </a>
                            @endif

                            @if(auth()->user()->isPanitia())
                                <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 text-sm font-semibold rounded {{ request()->routeIs('admin.dashboard') ? 'text-blue-700 bg-blue-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                    Dashboard
                                </a>
                                <a href="{{ route('admin.workshops.index') }}" class="px-3 py-2 text-sm font-semibold rounded {{ request()->routeIs('admin.workshops.*') ? 'text-blue-700 bg-blue-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                    Kelola Workshop
                                </a>
                                <a href="{{ route('admin.registrations.index') }}" class="px-3 py-2 text-sm font-semibold rounded {{ request()->routeIs('admin.registrations.*') ? 'text-blue-700 bg-blue-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                    Pendaftar & Uji N+1
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Bagian Kanan: Profil & Auth -->
                <div class="flex items-center gap-3">
                    @auth
                        <div class="hidden md:flex flex-col text-right">
                            <span class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</span>
                            <span class="text-xs font-semibold {{ auth()->user()->isPanitia() ? 'text-amber-700' : 'text-blue-700' }}">
                                [{{ strtoupper(auth()->user()->role) }}]
                            </span>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded cursor-pointer">
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="px-3 py-1.5 text-xs font-bold text-white bg-blue-700 hover:bg-blue-800 rounded">
                            Daftar Akun
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Menu Bar Kedua untuk Navigasi Mobile -->
        <div class="sm:hidden border-t border-slate-200 px-4 py-2 flex items-center gap-2 overflow-x-auto text-xs bg-slate-50">
            <a href="{{ route('catalog.index') }}" class="whitespace-nowrap px-2 py-1 font-semibold rounded {{ request()->routeIs('catalog.*') ? 'bg-blue-700 text-white' : 'text-slate-700 bg-white border border-slate-300' }}">
                Katalog
            </a>
            @auth
                @if(auth()->user()->isPeserta())
                    <a href="{{ route('registrations.index') }}" class="whitespace-nowrap px-2 py-1 font-semibold rounded {{ request()->routeIs('registrations.*') ? 'bg-blue-700 text-white' : 'text-slate-700 bg-white border border-slate-300' }}">
                        Tiket Saya
                    </a>
                @endif
                @if(auth()->user()->isPanitia())
                    <a href="{{ route('admin.dashboard') }}" class="whitespace-nowrap px-2 py-1 font-semibold rounded {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700 text-white' : 'text-slate-700 bg-white border border-slate-300' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.workshops.index') }}" class="whitespace-nowrap px-2 py-1 font-semibold rounded {{ request()->routeIs('admin.workshops.*') ? 'bg-blue-700 text-white' : 'text-slate-700 bg-white border border-slate-300' }}">
                        Workshop
                    </a>
                    <a href="{{ route('admin.registrations.index') }}" class="whitespace-nowrap px-2 py-1 font-semibold rounded {{ request()->routeIs('admin.registrations.*') ? 'bg-blue-700 text-white' : 'text-slate-700 bg-white border border-slate-300' }}">
                        Pendaftar & Uji N+1
                    </a>
                @endif
            @endauth
        </div>
    </nav>

    <!-- Notifikasi Flash Messages -->
    <main class="flex-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
            @if(session('success'))
                <div class="mb-6 p-4 rounded bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm">
                    <span class="font-bold">[BERHASIL]</span> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded bg-rose-50 border border-rose-300 text-rose-900 text-sm">
                    <span class="font-bold">[PERINGATAN / ERROR]</span> {{ session('error') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-6 p-4 rounded bg-amber-50 border border-amber-300 text-amber-900 text-sm">
                    <span class="font-bold">[INFORMASI PENTING]</span> {{ session('warning') }}
                </div>
            @endif

            @if(session('info'))
                <div class="mb-6 p-4 rounded bg-blue-50 border border-blue-300 text-blue-900 text-sm">
                    <span class="font-bold">[STATUS]</span> {{ session('info') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 rounded bg-rose-50 border border-rose-300 text-rose-900 text-sm">
                    <span class="font-bold block mb-1">[TERJADI KESALAHAN VALIDASI]</span>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <!-- Footer Standar Profesional LKPD -->
    <footer class="bg-white border-t border-slate-200 mt-12 py-8 text-xs text-slate-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <span class="text-base font-extrabold text-blue-800 tracking-tight block mb-2">WORKSHOP<span class="text-amber-600">TIX</span></span>
                    <p class="text-slate-600 leading-relaxed">
                        Aplikasi Sistem Pendaftaran dan Tiket Digital Workshop IT untuk pengujian kompetensi kejuruan Rekayasa Perangkat Lunak.
                    </p>
                </div>
                <div>
                    <span class="font-bold text-slate-900 block mb-2">STANDAR LKPD KOMPETENSI</span>
                    <ul class="space-y-1 text-slate-600">
                        <li>- Arsitektur MVC Laravel & MySQL</li>
                        <li>- Integrasi Payment Gateway Midtrans Sandbox</li>
                        <li>- Otorisasi Peran: Panitia & Peserta</li>
                        <li>- Analisis Optimasi Database Eager Loading vs N+1</li>
                    </ul>
                </div>
                <div>
                    <span class="font-bold text-slate-900 block mb-2">IDENTITAS SEKOLAH</span>
                    <p class="text-slate-600 leading-relaxed">
                        SMK PESAT IT XPRO - Rekayasa Perangkat Lunak<br>
                        Drill Pra Sertifikasi Siswa Kelas XI RPL<br>
                        Tahun Ajaran 2026/2027
                    </p>
                </div>
            </div>
            <div class="border-t border-slate-200 pt-4 flex flex-wrap justify-between items-center text-slate-500">
                <span>Hak Cipta Terlindungi &copy; 2026 WORKSHOPTIX SMK PESAT RPL.</span>
                <span>Dibangun untuk Standar Uji Sertifikasi Profesi.</span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
