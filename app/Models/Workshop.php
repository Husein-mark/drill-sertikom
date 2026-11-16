<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Workshop extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'category',
        'instructor_name',
        'instructor_title',
        'description',
        'schedule_date',
        'duration_hours',
        'location',
        'price',
        'quota',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'schedule_date' => 'datetime',
            'duration_hours' => 'integer',
            'price' => 'integer',
            'quota' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Boot function untuk auto-slug jika kosong.
     */
    protected static function booted(): void
    {
        static::creating(function (Workshop $workshop): void {
            if (empty($workshop->slug)) {
                $workshop->slug = Str::slug($workshop->title).'-'.Str::lower(Str::random(5));
            }
        });
    }

    /**
     * Relasi ke pendaftaran workshop.
     *
     * @return HasMany<Registration, $this>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Hitung sisa kuota yang masih tersedia.
     */
    public function getAvailableQuotaAttribute(): int
    {
        $taken = $this->registrations()
            ->whereIn('status', ['paid', 'pending'])
            ->count();

        return max(0, $this->quota - $taken);
    }

    /**
     * Format harga Rupiah.
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->price === 0) {
            return 'Gratis';
        }

        return 'Rp '.number_format($this->price, 0, ',', '.');
    }
}
