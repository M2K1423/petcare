<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'appointment_id',
        'doctor_id',
        'symptoms',
        'diagnosis',
        'treatment',
        'notes',
        'record_date',
    ];

    protected function casts(): array
    {
        return [
            'record_date' => 'date',
        ];
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function vaccinations(): HasMany
    {
        return $this->hasMany(Vaccination::class);
    }
}
