<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'fundraiser_id',
    'user_id',
    'donor_name',
    'amount',
    'currency',
    'message',
    'is_anonymous',
    'status',
    'reference',
])]
class Donation extends Model
{
    /** @use HasFactory<\Database\Factories\DonationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_anonymous' => 'boolean',
        ];
    }

    public function fundraiser(): BelongsTo
    {
        return $this->belongsTo(Fundraiser::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function getDonorDisplayAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Donateur anonyme';
        }

        return $this->user?->name ?? $this->donor_name ?? 'Donateur';
    }

    public function getAmountLabelAttribute(): string
    {
        return number_format((float) $this->amount, 0, ',', ' ').' '.$this->currency;
    }
}
