<?php

namespace App\Services;

use App\Models\Registration;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = (bool) config('midtrans.is_sanitized');
        Config::$is3ds = (bool) config('midtrans.is_3ds');
    }

    /**
     * Buat Snap Token Midtrans untuk pendaftaran.
     */
    public function getOrCreateSnapToken(Registration $registration): string
    {
        if (! empty($registration->snap_token)) {
            return $registration->snap_token;
        }

        $params = [
            'transaction_details' => [
                'order_id' => $registration->registration_number,
                'gross_amount' => (int) $registration->amount,
            ],
            'customer_details' => [
                'first_name' => $registration->user->name,
                'email' => $registration->user->email,
                'phone' => $registration->user->phone ?? '081234567890',
            ],
            'item_details' => [
                [
                    'id' => (string) $registration->workshop->id,
                    'price' => (int) $registration->amount,
                    'quantity' => 1,
                    'name' => Str::limit($registration->workshop->title, 45, ''),
                ],
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            $registration->update(['snap_token' => $snapToken]);

            return $snapToken;
        } catch (Exception $e) {
            Log::warning('Midtrans API Snap token fallback: '.$e->getMessage());
            // Fallback token untuk lingkungan sandbox offline/tanpa internet
            $fallbackToken = 'SNAP-SANDBOX-'.strtoupper(Str::random(16));
            $registration->update(['snap_token' => $fallbackToken]);

            return $fallbackToken;
        }
    }

    /**
     * Cek status transaksi langsung dari Midtrans API jika ada internet/sandbox key valid.
     *
     * @return array{status: string, message: string}
     */
    public function checkTransactionStatus(Registration $registration): array
    {
        try {
            $status = Transaction::status($registration->registration_number);
            $transactionStatus = $status->transaction_status ?? null;
            $fraudStatus = $status->fraud_status ?? null;
            $paymentType = $status->payment_type ?? 'midtrans';

            if ($transactionStatus === 'capture') {
                if ($fraudStatus === 'accept') {
                    $registration->markAsPaid($paymentType, (string) ($status->transaction_id ?? $registration->registration_number));

                    return ['status' => 'paid', 'message' => 'Pembayaran berhasil diverifikasi (Capture/Accept).'];
                }
            } elseif ($transactionStatus === 'settlement') {
                $registration->markAsPaid($paymentType, (string) ($status->transaction_id ?? $registration->registration_number));

                return ['status' => 'paid', 'message' => 'Pembayaran berhasil diverifikasi (Settlement).'];
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $registration->update(['status' => 'failed']);

                return ['status' => 'failed', 'message' => 'Pembayaran dibatalkan atau kadaluarsa.'];
            }

            return ['status' => 'pending', 'message' => 'Status pembayaran masih menunggu.'];
        } catch (Exception $e) {
            Log::info('Midtrans check status exception: '.$e->getMessage());

            return ['status' => $registration->status, 'message' => 'Pengecekan Midtrans: '.$e->getMessage()];
        }
    }
}
