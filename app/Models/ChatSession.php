<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class ChatSession extends Model
{
    protected $fillable = [
        'owner_id',
        'staff_id',
        'status',
    ];

    /* ── Relations ── */

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }

    /* ── Scopes ── */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('owner_id', $userId)
              ->orWhere('staff_id', $userId);
        });
    }

    /* ── Helpers ── */

    public function isParticipant(int $userId): bool
    {
        return $this->owner_id === $userId || $this->staff_id === $userId;
    }

    public function getOtherUser(int $currentUserId): ?User
    {
        return $currentUserId === $this->owner_id ? $this->staff : $this->owner;
    }
}
