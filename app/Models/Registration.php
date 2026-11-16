<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Registration extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'registration_number',
        'ticket_code',
        'user_id',
        'workshop_id',
        'amount',
        'status',
        'snap_token',
        'payment_type',
        'payment_reference',
        'paid_at',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Boot function untuk auto nomor registrasi.
     */
    protected static function booted(): void
    {
        static::creating(function (Registration $registration): void {
            if (empty($registration->registration_number)) {
                $registration->registration_number = 'REG-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
            }
        });
    }

    /**
     * Relasi ke User pemesan.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Workshop.
     *
     * @return BelongsTo<Workshop, $this>
     */
    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    /**
     * Cek apakah status pembayaran lunas.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Tandai pendaftaran sebagai lunas dan terbitkan tiket.
     */
    public function markAsPaid(?string $paymentType = 'midtrans', ?string $reference = null): void
    {
        if (empty($this->ticket_code)) {
            $this->ticket_code = 'TIX-'.now()->format('Ymd').'-'.strtoupper(Str::random(8));
        }

        $this->status = 'paid';
        $this->payment_type = $paymentType ?? 'midtrans';
        $this->payment_reference = $reference ?? ('REF-'.strtoupper(Str::random(10)));
        $this->paid_at = now();
        $this->save();
    }

    /**
     * Format Rupiah nominal pembayaran.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp '.number_format($this->amount, 0, ',', '.');
    }
}
