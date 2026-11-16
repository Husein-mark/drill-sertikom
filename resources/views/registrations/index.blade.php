@extends('layouts.app')

@section('title', 'Tiket & Pendaftaran Saya - WORKSHOPTIX')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6 flex flex-wrap justify-between items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Tiket & Pendaftaran Saya</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar seluruh workshop IT yang Anda ikuti dan status verifikasi pembayarannya</p>
        </div>
        <a href="{{ route('catalog.index') }}" class="py-2 px-4 bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs rounded transition">
            Pilih Workshop Lainnya &rarr;
        </a>
    </div>

    @if($registrations->isEmpty())
        <div class="bg-white border border-slate-300 rounded p-12 text-center">
            <span class="block text-sm font-bold text-slate-800 mb-1">[BELUM ADA TIKET TERDAFTAR]</span>
            <p class="text-xs text-slate-500 max-w-md mx-auto mb-6">
                Anda belum pernah mendaftar pada workshop manapun. Silakan jelajahi katalog untuk menemukan topik workshop yang ingin Anda ikuti.
            </p>
            <a href="{{ route('catalog.index') }}" class="inline-block py-2.5 px-6 bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs rounded">
                Buka Katalog Workshop Sekarang
            </a>
        </div>
    @else
        <div class="bg-white border border-slate-300 rounded shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900 text-white uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4">No. Registrasi</th>
                            <th class="py-3 px-4">Workshop</th>
                            <th class="py-3 px-4">Jadwal Pelaksanaan</th>
                            <th class="py-3 px-4">Total Biaya</th>
                            <th class="py-3 px-4 text-center">Status Transaksi</th>
                            <th class="py-3 px-4 text-right">Aksi Dokumen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($registrations as $reg)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-4 font-mono font-bold text-slate-800">
                                    {{ $reg->registration_number }}
                                    @if($reg->ticket_code)
                                        <span class="block text-[10px] text-blue-700 font-normal mt-0.5">Tiket: {{ $reg->ticket_code }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <span class="font-bold text-slate-900 text-sm block">{{ $reg->workshop->title }}</span>
                                    <span class="text-slate-500 text-[11px]">{{ $reg->workshop->instructor_name }}</span>
                                </td>
                                <td class="py-4 px-4 text-slate-700">
                                    <span class="font-semibold block">{{ $reg->workshop->schedule_date->translatedFormat('d M Y') }}</span>
                                    <span class="text-[11px] text-slate-500">{{ $reg->workshop->schedule_date->format('H:i') }} WIB</span>
                                </td>
                                <td class="py-4 px-4 font-bold text-blue-900">
                                    {{ $reg->formatted_amount }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    @if($reg->isPaid())
                                        <span class="inline-block px-2.5 py-1 rounded bg-emerald-100 text-emerald-900 border border-emerald-300 font-extrabold text-[10px] uppercase">
                                            [LUNAS / TERBIT]
                                        </span>
                                    @elseif($reg->status === 'pending')
                                        <span class="inline-block px-2.5 py-1 rounded bg-amber-100 text-amber-900 border border-amber-300 font-extrabold text-[10px] uppercase">
                                            [MENUNGGU BAYAR]
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-1 rounded bg-rose-100 text-rose-900 border border-rose-300 font-extrabold text-[10px] uppercase">
                                            [{{ strtoupper($reg->status) }}]
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right">
                                    @if($reg->isPaid())
                                        <a href="{{ route('registrations.ticket', $reg) }}" class="inline-block py-1.5 px-3 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded transition">
                                            Buka Tiket Resmi
                                        </a>
                                    @elseif($reg->status === 'pending')
                                        <a href="{{ route('registrations.payment', $reg) }}" class="inline-block py-1.5 px-3 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded transition">
                                            Bayar Sekarang
                                        </a>
                                    @else
                                        <span class="text-slate-400 font-medium">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200">
                {{ $registrations->links() }}
            </div>
        </div>
    @endif

</div>
@endsection
