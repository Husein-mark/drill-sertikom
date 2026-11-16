<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Workshop;
use App\Services\MidtransService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function __construct(
        protected MidtransService $midtransService
    ) {}

    /**
     * Tampilkan halaman konfirmasi sebelum mendaftar workshop.
     */
    public function create(Workshop $workshop): View|RedirectResponse
    {
        $user = Auth::user();

        // Validasi: Tolak jika sudah pernah mendaftar
        $existing = Registration::query()
            ->where('user_id', $user->id)
            ->where('workshop_id', $workshop->id)
            ->first();

        if ($existing) {
            return redirect()->route('registrations.payment', $existing)
                ->with('warning', 'Anda sudah terdaftar di workshop ini. Silakan selesaikan pembayaran atau lihat tiket Anda.');
        }

        // Validasi: Cek ketersediaan kuota
        if ($workshop->available_quota <= 0) {
            return redirect()->route('catalog.show', $workshop)
                ->with('error', 'Pendaftaran gagal: Kuota peserta untuk workshop ini sudah penuh.');
        }

        return view('registrations.create', [
            'workshop' => $workshop,
            'user' => $user,
        ]);
    }

    /**
     * Simpan pendaftaran baru ke database (Nominal diambil langsung dari server).
     */
    public function store(Request $request, Workshop $workshop): RedirectResponse
    {
        $user = Auth::user();

        // Validasi Form & Tolak Pendaftaran Ganda
        $existing = Registration::query()
            ->where('user_id', $user->id)
            ->where('workshop_id', $workshop->id)
            ->exists();

        if ($existing) {
            return redirect()->route('registrations.index')
                ->with('error', 'Pendaftaran Ditolak: Anda sudah terdaftar di workshop ini sebelumnya. Pendaftaran ganda tidak diizinkan.');
        }

        // Cek kuota workshop
        if ($workshop->available_quota <= 0) {
            return redirect()->route('catalog.show', $workshop)
                ->with('error', 'Pendaftaran Gagal: Kuota kursi peserta telah terisi penuh.');
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
            'agreement' => ['required', 'accepted'],
        ], [
            'agreement.accepted' => 'Anda harus menyetujui syarat & ketentuan pendaftaran.',
        ]);

        // Catat pendaftaran baru dengan nominal valid dari server
        $registration = Registration::create([
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
            'amount' => $workshop->price,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Buat Snap Token Midtrans dari backend
        $this->midtransService->getOrCreateSnapToken($registration);

        return redirect()->route('registrations.payment', $registration)
            ->with('success', 'Pendaftaran berhasil dicatat. Silakan lakukan pembayaran untuk menerbitkan tiket resmi.');
    }

    /**
     * Tampilkan daftar pendaftaran dan tiket milik peserta yang login.
     */
    public function index(): View
    {
        $user = Auth::user();

        $registrations = Registration::query()
            ->with(['workshop'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('registrations.index', [
            'registrations' => $registrations,
        ]);
    }

    /**
     * Halaman pembayaran & status transaksi (Midtrans Sandbox).
     */
    public function payment(Registration $registration): View|RedirectResponse
    {
        $user = Auth::user();

        // Otorisasi kepemilikan data pendaftaran
        if (! $user->isPanitia() && $registration->user_id !== $user->id) {
            abort(403, 'Akses ditolak. Anda tidak berhak melihat rincian pembayaran ini.');
        }

        // Jika sudah lunas, langsung arahkan ke tiket digital
        if ($registration->isPaid()) {
            return redirect()->route('registrations.ticket', $registration)
                ->with('success', 'Pembayaran transaksi ini sudah lunas. Tiket digital Anda telah aktif.');
        }

        // Siapkan snap token
        $snapToken = $this->midtransService->getOrCreateSnapToken($registration);

        return view('registrations.payment', [
            'registration' => $registration,
            'workshop' => $registration->workshop,
            'snapToken' => $snapToken,
            'clientKey' => config('midtrans.client_key'),
        ]);
    }

    /**
     * Tampilkan tiket digital resmi (HANYA setelah lunas).
     */
    public function ticket(Registration $registration): View|RedirectResponse
    {
        $user = Auth::user();

        // Otorisasi: hanya pemilik pendaftaran atau panitia yang berhak melihat
        if (! $user->isPanitia() && $registration->user_id !== $user->id) {
            abort(403, 'Akses ditolak. Tiket digital ini milik peserta lain.');
        }

        // KETENTUAN KHUSUS: Tampilkan tiket HANYA setelah lunas
        if (! $registration->isPaid()) {
            return redirect()->route('registrations.payment', $registration)
                ->with('error', 'Tiket digital belum dapat diterbitkan karena status pembayaran masih belum lunas.');
        }

        return view('registrations.ticket', [
            'registration' => $registration,
            'workshop' => $registration->workshop,
            'user' => $registration->user,
        ]);
    }
}
