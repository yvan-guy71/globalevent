<?php

namespace App\Models;

use Database\Factories\EventFactory;
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
    'type',
    'description',
    'starts_at',
    'ends_at',
    'venue',
    'address',
    'city',
    'country',
    'price',
    'currency',
    'capacity',
    'image',
    'contact_name',
    'contact_email',
    'contact_phone',
    'status',
    'is_featured',
    'scan_token',
    'published_at',
])]
class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    protected $appends = ['image_url', 'is_free', 'type_label', 'status_label'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'published_at' => 'datetime',
            'price' => 'decimal:2',
            'capacity' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(CompetitionCandidate::class);
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /* ------------------------------------------------------------------ */
    /* Scopes                                                              */
    /* ------------------------------------------------------------------ */

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>=', now()->startOfDay());
    }

    public function scopePast($query)
    {
        return $query->where('starts_at', '<', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOfCity($query, ?string $city)
    {
        return $city ? $query->where('city', $city) : $query;
    }

    public function scopeOfCountry($query, ?string $country)
    {
        return $country ? $query->where('country', $country) : $query;
    }

    public function scopeOfType($query, ?string $type)
    {
        return $type && array_key_exists($type, config('events.types', []))
            ? $query->where('type', $type)
            : $query;
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $like = '%'.$term.'%';
            $q->where('title', 'like', $like)
                ->orWhere('description', 'like', $like)
                ->orWhere('venue', 'like', $like)
                ->orWhere('city', 'like', $like)
                ->orWhere('country', 'like', $like);
        });
    }

    /* ------------------------------------------------------------------ */
    /* Helpers                                                             */
    /* ------------------------------------------------------------------ */

    public static function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title) ?: 'evenement';
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

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (Str::startsWith($this->image, ['http://', 'https://', '/'])) {
            return $this->image;
        }

        return asset('storage/'.$this->image);
    }

    public function getFallbackImageAttribute(): string
    {
        return config("events.types.{$this->type}.image", 'images/categories/DJ.jpg');
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

    public function getTypeLabelAttribute(): string
    {
        return config("events.types.{$this->type}.label", 'Autre');
    }

    public function getStatusLabelAttribute(): string
    {
        return config("events.statuses.{$this->status}", 'Publié');
    }

    public function isPast(): bool
    {
        return $this->starts_at->isPast();
    }

    public function isCompetition(): bool
    {
        return $this->type === 'competition';
    }

    public function hasTicketing(): bool
    {
        if (array_key_exists('active_ticket_types_count', $this->attributes)) {
            return (int) $this->attributes['active_ticket_types_count'] > 0;
        }

        return $this->ticketTypes()->active()->exists();
    }

    public function isOwnedBy($user): bool
    {
        return $user && $this->user_id === $user->id;
    }

    public function canBeManagedBy(?User $user): bool
    {
        return $user !== null && ($this->isOwnedBy($user) || $user->isAdmin());
    }

    /* ------------------------------------------------------------------ */
    /* Partager le scanner (accès tiers sans compte)                       */
    /* ------------------------------------------------------------------ */

    public function hasScanShare(): bool
    {
        return ! empty($this->scan_token);
    }

    public function generateScanToken(): string
    {
        do {
            $token = Str::random(48);
        } while (static::query()->where('scan_token', $token)->exists());

        $this->scan_token = $token;
        $this->save();

        return $token;
    }

    public function revokeScanToken(): void
    {
        $this->scan_token = null;
        $this->save();
    }

    public function scanUrl(): ?string
    {
        return $this->hasScanShare()
            ? route('scan.show', ['event' => $this->slug, 'token' => $this->scan_token])
            : null;
    }
}
