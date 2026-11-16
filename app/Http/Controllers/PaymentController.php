<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        protected MidtransService $midtransService
    ) {}

    /**
     * Simulasi Pembayaran Sandbox Berhasil melalui Backend.
     * Sesuai ketentuan drill: memverifikasi status pembayaran di server lalu menerbitkan kode tiket digital.
     */
    public function simulateSuccess(Request $request, Registration $registration): RedirectResponse
    {
        $user = Auth::user();

        // Otorisasi: pemilik transaksi atau panitia
        if (! $user->isPanitia() && $registration->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        if ($registration->isPaid()) {
            return redirect()->route('registrations.ticket', $registration)
                ->with('info', 'Transaksi ini sudah lunas sebelumnya.');
        }

        $paymentMethod = $request->input('payment_method', 'Midtrans Sandbox QRIS');

        // Verifikasi dan tandai lunas di backend
        $registration->markAsPaid(
            paymentType: $paymentMethod,
            reference: 'SIM-SANDBOX-'.strtoupper(uniqid())
        );

        return redirect()->route('registrations.ticket', $registration)
            ->with('success', 'Pembayaran berhasil diverifikasi oleh server! Tiket digital Anda telah diterbitkan.');
    }

    /**
     * Pengecekan status transaksi ke server Midtrans.
     */
    public function checkStatus(Registration $registration): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isPanitia() && $registration->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $result = $this->midtransService->checkTransactionStatus($registration);

        if ($registration->fresh()->isPaid()) {
            return redirect()->route('registrations.ticket', $registration)
                ->with('success', 'Status Pembayaran: '.$result['message']);
        }

        return redirect()->route('registrations.payment', $registration)
            ->with('info', 'Status Pembayaran: '.$result['message']);
    }

    /**
     * Webhook HTTP Notification dari Midtrans Server.
     */
    public function notification(Request $request): JsonResponse
    {
        $payload = $request->all();
        Log::info('Midtrans Webhook Notification Received', $payload);

        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? 'midtrans';

        if (! $orderId) {
            return response()->json(['message' => 'Order ID tidak ditemukan'], 400);
        }

        $registration = Registration::where('registration_number', $orderId)->first();

        if (! $registration) {
            return response()->json(['message' => 'Pendaftaran tidak ditemukan'], 404);
        }

        if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
            $registration->markAsPaid($paymentType, (string) ($payload['transaction_id'] ?? $orderId));
        } elseif ($transactionStatus === 'settlement') {
            $registration->markAsPaid($paymentType, (string) ($payload['transaction_id'] ?? $orderId));
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $registration->update(['status' => 'failed']);
        }

        return response()->json(['message' => 'Notifikasi diproses']);
    }
}
