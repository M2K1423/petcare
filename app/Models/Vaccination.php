<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vaccination extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'medical_record_id',
        'medicine_id',
        'vaccine_name',
        'vaccinated_on',
        'next_due_on',
        'batch_number',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'vaccinated_on' => 'date',
            'next_due_on' => 'date',
        ];
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
