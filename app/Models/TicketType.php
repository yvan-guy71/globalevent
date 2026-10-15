<?php

namespace App\Models;

use Database\Factories\TicketTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'event_id',
    'name',
    'description',
    'price',
    'currency',
    'quantity',
    'sold',
    'is_active',
    'sort_order',
])]
class TicketType extends Model
{
    /** @use HasFactory<TicketTypeFactory> */
    use HasFactory;

    protected $appends = ['price_label', 'is_free', 'remaining', 'is_sold_out'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity' => 'integer',
            'sold' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /* ------------------------------------------------------------------ */
    /* Relations                                                           */
    /* ------------------------------------------------------------------ */

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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    /* ------------------------------------------------------------------ */
    /* Helpers                                                             */
    /* ------------------------------------------------------------------ */

    public function remaining(): ?int
    {
        if ($this->quantity === null) {
            return null;
        }

        return max(0, $this->quantity - $this->sold);
    }

    public function isSoldOut(): bool
    {
        return $this->quantity !== null && $this->remaining() <= 0;
    }

    public function canPurchase(int $quantity): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->quantity === null) {
            return true;
        }

        return $quantity <= $this->remaining();
    }

    public function getRemainingAttribute(): ?int
    {
        return $this->remaining();
    }

    public function getIsSoldOutAttribute(): bool
    {
        return $this->isSoldOut();
    }

    public function getIsFreeAttribute(): bool
    {
        return (float) $this->price <= 0;
    }

    public function getPriceLabelAttribute(): string
    {
        if ($this->is_free) {
            return 'Gratuit';
        }

        return number_format((float) $this->price, 0, ',', ' ').' '.$this->currency;
    }
}
