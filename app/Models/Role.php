<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    public const OWNER = 'owner';
    public const VET = 'vet';
    public const RECEPTIONIST = 'receptionist';
    public const ADMIN = 'admin';
    public const AI_ASSISTANT = 'ai_assistant';

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
