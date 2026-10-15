<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['event_id', 'candidate_id', 'user_id', 'value'])]
class CompetitionVote extends Model
{
    public const FOR = 1;

    public const AGAINST = -1;

    protected function casts(): array
    {
        return [
            'value' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(CompetitionCandidate::class, 'candidate_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isFor(): bool
    {
        return $this->value === self::FOR;
    }

    public function isAgainst(): bool
    {
        return $this->value === self::AGAINST;
    }
}
