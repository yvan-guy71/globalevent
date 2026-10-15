<?php

namespace App\Models;

use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'order_id',
    'order_item_id',
    'ticket_type_id',
    'event_id',
    'user_id',
    'code',
    'holder_name',
    'status',
    'checked_in_at',
    'checked_in_by',
])]
class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory;

    protected $appends = ['is_valid', 'is_used'];

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
        ];
    }

    /* ------------------------------------------------------------------ */
    /* Relations                                                           */
    /* ------------------------------------------------------------------ */

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    /* ------------------------------------------------------------------ */
    /* Scopes                                                              */
    /* ------------------------------------------------------------------ */

    public function scopeValid($query)
    {
        return $query->where('status', 'valid');
    }

    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }

    /* ------------------------------------------------------------------ */
    /* Helpers                                                             */
    /* ------------------------------------------------------------------ */

    public static function generateCode(): string
    {
        do {
            $code = 'TKT-'.Str::upper(Str::random(10));
        } while (static::query()->where('code', $code)->exists());

        return $code;
    }

    public function getIsValidAttribute(): bool
    {
        return $this->status === 'valid';
    }

    public function getIsUsedAttribute(): bool
    {
        return $this->status === 'used';
    }

    public function markUsed(?User $by = null): void
    {
        $this->status = 'used';
        $this->checked_in_at = now();
        $this->checked_in_by = $by?->id;
        $this->save();
    }

    public function holder(): string
    {
        return $this->holder_name ?: ($this->user?->name ?? 'Titulaire');
    }
}
