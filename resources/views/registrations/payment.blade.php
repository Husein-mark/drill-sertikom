@extends('layouts.app')

@section('title', 'Pembayaran Transaksi ' . $registration->registration_number . ' - WORKSHOPTIX')

@push('head')
<!-- Script Midtrans Snap Sandbox -->
<script type="text/javascript"
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ $clientKey }}"></script>
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('registrations.index') }}" class="inline-flex items-center text-xs font-bold text-blue-700 hover:text-blue-900 bg-white border border-slate-300 px-3 py-1.5 rounded">
            &larr; Lihat Seluruh Pendaftaran Saya
        </a>
        <span class="text-xs font-mono font-bold text-slate-500">ID: {{ $registration->registration_number }}</span>
    </div>

    <div class="bg-white border border-slate-300 rounded shadow-sm overflow-hidden mb-8">
        <!-- Header Tagihan -->
        <div class="bg-slate-900 text-white p-6 border-b border-slate-800 flex flex-wrap justify-between items-center gap-4">
            <div>
                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider block mb-1">LANGKAH 2 DARI 2 • GERBANG PEMBAYARAN</span>
                <h1 class="text-xl font-bold">Tagihan Pendaftaran Workshop</h1>
                <p class="text-xs text-slate-300 mt-1">Selesaikan transaksi untuk menerbitkan e-ticket resmi</p>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded bg-amber-500 text-slate-950 font-black text-xs uppercase tracking-wider">
                    [STATUS: MENUNGGU PEMBAYARAN]
                </span>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-12 gap-8">

            <!-- Rincian Pesanan (Kiri) -->
            <div class="md:col-span-7 space-y-4">
                <div class="border border-slate-200 rounded p-4 text-xs space-y-3">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">RINCIAN PESANAN</span>
                    <div>
                        <span class="text-slate-500 block">Nomor Registrasi:</span>
                        <span class="font-mono font-bold text-slate-900 text-sm">{{ $registration->registration_number }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Judul Workshop:</span>
                        <span class="font-bold text-slate-900 text-sm leading-snug">{{ $workshop->title }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                        <div>
                            <span class="text-slate-500 block">Nama Peserta:</span>
                            <span class="font-bold text-slate-800">{{ $registration->user->name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Jadwal Acara:</span>
                            <span class="font-bold text-slate-800">{{ $workshop->schedule_date->translatedFormat('d M Y, H:i') }} WIB</span>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded p-4 flex justify-between items-center">
                    <div>
                        <span class="text-xs font-bold text-slate-600 uppercase block">TOTAL NOMINAL SERVER</span>
                        <span class="text-[11px] text-slate-500">Harga resmi tanpa biaya tambahan</span>
                    </div>
                    <div class="text-2xl font-black text-blue-900">
                        {{ $registration->formatted_amount }}
                    </div>
                </div>

                <!-- Petunjuk Alur Midtrans Sandbox -->
                <div class="p-4 bg-blue-50 border border-blue-200 rounded text-xs text-blue-950 space-y-1.5">
                    <span class="font-bold block">[KETENTUAN UJI DRILL SERTIFIKASI]</span>
                    <p class="leading-relaxed">
                        1. Transaksi berjalan pada lingkungan <strong>Midtrans Sandbox (Uji Coba)</strong>.
                    </p>
                    <p class="leading-relaxed">
                        2. Anda dapat menggunakan tombol popup Midtrans Snap jika terhubung ke internet dengan kredensial sandbox resmi, atau gunakan <strong>Simulasi Verifikasi Backend</strong> di sebelah kanan untuk langsung mensimulasikan callback pelunasan dari server.
                    </p>
                </div>
            </div>

            <!-- Panel Pembayaran & Simulator (Kanan) -->
            <div class="md:col-span-5 space-y-5 border-t md:border-t-0 md:border-l border-slate-200 md:pl-8 pt-4 md:pt-0">

                <!-- Opsi 1: Midtrans Snap Asli -->
                <div class="border border-slate-300 rounded p-4 bg-white">
                    <span class="text-xs font-bold text-slate-900 uppercase tracking-wider block mb-1">METODE A: MIDTRANS SNAP POPUP</span>
                    <p class="text-[11px] text-slate-600 mb-3">Buka antarmuka pembayaran Midtrans Snap secara langsung di browser.</p>
                    
                    <button type="button" id="pay-button" class="w-full py-2.5 px-4 bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs rounded cursor-pointer transition">
                        Buka Popup Midtrans Snap
                    </button>
                    <span class="block text-[10px] text-slate-400 mt-2 font-mono truncate">Token: {{ $snapToken }}</span>
                </div>

                <!-- Opsi 2: Simulator Verifikasi Backend (Sesuai LKPD) -->
                <div class="border border-amber-300 rounded p-4 bg-amber-50">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-amber-950 uppercase tracking-wider">METODE B: SIMULASI VERIFIKASI BACKEND</span>
                    </div>
                    <p class="text-[11px] text-amber-900 mb-3 leading-relaxed">
                        Sesuai poin 04 LKPD: Mensimulasikan pembayaran lunas di sandbox dan memverifikasi status melalui backend Laravel.
                    </p>

                    <form method="POST" action="{{ route('payments.simulate', $registration) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label for="payment_method" class="block text-[11px] font-bold text-amber-950 mb-1">Pilih Kanal Simulasi</label>
                            <select name="payment_method" id="payment_method" class="w-full px-2.5 py-1.5 text-xs border border-amber-300 rounded bg-white text-slate-800">
                                <option value="Midtrans Sandbox QRIS">QRIS Sandbox (Instan Settlement)</option>
                                <option value="Midtrans Sandbox BCA Virtual Account">BCA Virtual Account Sandbox</option>
                                <option value="Midtrans Sandbox Mandiri Bill">Mandiri Bill Payment Sandbox</option>
                                <option value="Midtrans Sandbox GoPay">GoPay Sandbox</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full py-2.5 px-4 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded cursor-pointer transition">
                            Simulasikan Pembayaran Berhasil &rarr;
                        </button>
                    </form>
                </div>

                <!-- Pengecekan Status Manual -->
                <div class="pt-2 text-center">
                    <a href="{{ route('payments.check', $registration) }}" class="text-xs font-semibold text-slate-600 hover:text-blue-700 underline">
                        Sinkronisasi / Cek Status Pembayaran ke Server
                    </a>
                </div>

            </div>

        </div>
    </div>

</div>

@push('scripts')
<script type="text/javascript">
    const payButton = document.getElementById('pay-button');
    const snapToken = "{{ $snapToken }}";

    if (payButton) {
        payButton.addEventListener('click', function () {
            if (typeof window.snap === 'undefined') {
                alert('Midtrans Snap SDK tidak termuat. Anda dapat menggunakan tombol Simulasi Verifikasi Backend di bawah.');
                return;
            }

            window.snap.pay(snapToken, {
                onSuccess: function(result){
                    console.log('Payment success:', result);
                    window.location.href = "{{ route('payments.check', $registration) }}";
                },
                onPending: function(result){
                    console.log('Payment pending:', result);
                    window.location.reload();
                },
                onError: function(result){
                    console.log('Payment error:', result);
                    alert('Pembayaran gagal atau dibatalkan.');
                },
                onClose: function(){
                    console.log('Snap window closed without completing payment');
                }
            });
        });
    }
</script>
@endpush
@endsection
