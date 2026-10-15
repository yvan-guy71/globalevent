<?php

namespace App\Models;

use Database\Factories\FundraiserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'title',
    'slug',
    'description',
    'goal_amount',
    'currency',
    'deadline',
    'image',
    'contact_name',
    'contact_email',
    'contact_phone',
    'status',
    'is_featured',
])]
class Fundraiser extends Model
{
    /** @use HasFactory<FundraiserFactory> */
    use HasFactory;

    protected $appends = ['image_url', 'goal_label', 'raised_label', 'progress_percent', 'is_reached'];

    protected function casts(): array
    {
        return [
            'goal_amount' => 'decimal:2',
            'deadline' => 'date',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /* ------------------------------------------------------------------ */
    /* Relations                                                           */
    /* ------------------------------------------------------------------ */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    /* ------------------------------------------------------------------ */
    /* Scopes                                                              */
    /* ------------------------------------------------------------------ */

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $like = '%'.$term.'%';
            $q->where('title', 'like', $like)->orWhere('description', 'like', $like);
        });
    }

    public function scopeWithProgress($query)
    {
        return $query->withCount('donations')->withSum(
            ['donations as raised_amount' => fn ($q) => $q->where('status', 'paid')],
            'amount'
        );
    }

    /* ------------------------------------------------------------------ */
    /* Helpers                                                             */
    /* ------------------------------------------------------------------ */

    public static function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title) ?: 'cagnotte';
        $base = $slug;
        $i = 1;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function getRaisedAmountAttribute(): float
    {
        if (array_key_exists('raised_amount', $this->attributes)) {
            return (float) ($this->attributes['raised_amount'] ?? 0);
        }

        return (float) $this->donations()->where('status', 'paid')->sum('amount');
    }

    public function getDonorsCountAttribute(): int
    {
        return (int) ($this->attributes['donations_count'] ?? $this->donations()->where('status', 'paid')->count());
    }

    public function getProgressPercentAttribute(): int
    {
        $goal = (float) $this->goal_amount;

        if ($goal <= 0) {
            return 0;
        }

        return (int) min(100, round(($this->raised_amount / $goal) * 100));
    }

    public function getIsReachedAttribute(): bool
    {
        return (float) $this->goal_amount > 0 && $this->raised_amount >= (float) $this->goal_amount;
    }

    public function getGoalLabelAttribute(): string
    {
        return number_format((float) $this->goal_amount, 0, ',', ' ').' '.$this->currency;
    }

    public function getRaisedLabelAttribute(): string
    {
        return number_format($this->raised_amount, 0, ',', ' ').' '.$this->currency;
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (Str::startsWith($this->image, ['http://', 'https://', '/'])) {
            return $this->image;
        }

        return asset('storage/'.$this->image);
    }

    public function getStatusLabelAttribute(): string
    {
        return ['open' => 'Ouverte', 'closed' => 'Clôturée'][$this->status] ?? 'Ouverte';
    }

    public function isExpired(): bool
    {
        return $this->deadline !== null && $this->deadline->isPast();
    }

    public function isOpen(): bool
    {
        return $this->status === 'open' && ! $this->isExpired();
    }

    public function daysLeft(): ?int
    {
        if (! $this->deadline) {
            return null;
        }

        return max(0, (int) now()->startOfDay()->diffInDays($this->deadline->startOfDay(), false));
    }

    public function canBeManagedBy(?User $user): bool
    {
        return $user !== null && ($this->user_id === $user->id || $user->isAdmin());
    }
}
