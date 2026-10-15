<?php

namespace App\Models;

use Database\Factories\CompetitionCandidateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'event_id',
    'user_id',
    'name',
    'slug',
    'description',
    'image',
    'contact_phone',
    'sort_order',
    'is_active',
])]
class CompetitionCandidate extends Model
{
    /** @use HasFactory<CompetitionCandidateFactory> */
    use HasFactory;

    protected $appends = ['image_url', 'score'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    /* ------------------------------------------------------------------ */
    /* Relations                                                           */
    /* ------------------------------------------------------------------ */

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(CompetitionVote::class, 'candidate_id');
    }

    public function voteOf(?User $user): ?CompetitionVote
    {
        if (! $user) {
            return null;
        }

        return $this->votes->firstWhere('user_id', $user->id);
    }

    /* ------------------------------------------------------------------ */
    /* Scopes                                                              */
    /* ------------------------------------------------------------------ */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithScore($query)
    {
        return $query->withCount([
            'votes as votes_up' => fn ($q) => $q->where('value', 1),
            'votes as votes_down' => fn ($q) => $q->where('value', -1),
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* Helpers                                                             */
    /* ------------------------------------------------------------------ */

    public static function generateSlug(string $name, int $eventId, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name) ?: 'candidat';
        $base = $slug;
        $i = 1;

        while (static::query()
            ->where('event_id', $eventId)
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function getVotesUpAttribute(): int
    {
        return (int) ($this->attributes['votes_up'] ?? 0);
    }

    public function getVotesDownAttribute(): int
    {
        return (int) ($this->attributes['votes_down'] ?? 0);
    }

    public function getScoreAttribute(): int
    {
        if (array_key_exists('votes_up', $this->attributes) || array_key_exists('votes_down', $this->attributes)) {
            return $this->votes_up - $this->votes_down;
        }

        $votes = $this->votes()->selectRaw('COALESCE(SUM(value), 0) as total')->value('total');

        return (int) $votes;
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
}
