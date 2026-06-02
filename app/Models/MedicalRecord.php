<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $appends = [
        'prescription',
    ];

    protected $fillable = [
        'record_code',
        'pet_id',
        'appointment_id',
        'doctor_id',
        'temperature_c',
        'weight_kg',
        'heart_rate_bpm',
        'symptoms',
        'abnormal_signs',
        'diagnosis',
        'preliminary_diagnosis',
        'final_diagnosis',
        'pathology',
        'severity_level',
        'treatment',
        'treatment_protocol',
        'disease_progress',
        'follow_up_plan',
        'service_orders',
        'prescriptions',
        'procedures',
        'progress_logs',
        'signed_off_at',
        'notes',
        'record_date',
    ];

    protected function casts(): array
    {
        return [
            'record_date' => 'date',
            'temperature_c' => 'decimal:1',
            'weight_kg' => 'decimal:2',
            'heart_rate_bpm' => 'integer',
            'service_orders' => 'array',
            'prescriptions' => 'array',
            'procedures' => 'array',
            'progress_logs' => 'array',
            'signed_off_at' => 'datetime',
        ];
    }

    public function getPrescriptionAttribute(): ?string
    {
        return $this->treatment;
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function vaccinations(): HasMany
    {
        return $this->hasMany(Vaccination::class);
    }
}
