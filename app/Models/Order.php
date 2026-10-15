<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'event_id',
    'reference',
    'total_amount',
    'currency',
    'status',
    'paid_at',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $appends = ['total_label', 'status_label', 'tickets_count'];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    /* ------------------------------------------------------------------ */
    /* Relations                                                           */
    /* ------------------------------------------------------------------ */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /* ------------------------------------------------------------------ */
    /* Scopes                                                              */
    /* ------------------------------------------------------------------ */

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /* ------------------------------------------------------------------ */
    /* Helpers                                                             */
    /* ------------------------------------------------------------------ */

    public static function generateReference(): string
    {
        do {
            $reference = 'CMD-'.Str::upper(Str::random(10));
        } while (static::query()->where('reference', $reference)->exists());

        return $reference;
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function markPaid(): void
    {
        $this->status = 'paid';
        $this->paid_at = now();
        $this->save();
    }

    public function getTotalLabelAttribute(): string
    {
        return number_format((float) $this->total_amount, 0, ',', ' ').' '.$this->currency;
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'pending' => 'En attente',
            'paid' => 'Payée',
            'cancelled' => 'Annulée',
        ][$this->status] ?? 'En attente';
    }

    public function getTicketsCountAttribute(): int
    {
        return (int) ($this->attributes['tickets_count'] ?? $this->tickets()->count());
    }
}
