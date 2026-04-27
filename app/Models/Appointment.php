<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasFactory;

    public const WORKFLOW_AWAITING_EXAM = 'awaiting_exam';
    public const WORKFLOW_EXAMINING = 'examining';
    public const WORKFLOW_AWAITING_LAB = 'awaiting_lab';
    public const WORKFLOW_TREATING = 'treating';
    public const WORKFLOW_COMPLETED = 'completed';
    public const WORKFLOW_FOLLOW_UP = 'follow_up';

    protected $fillable = [
        'pet_id',
        'owner_id',
        'doctor_id',
        'service_id',
        'appointment_at',
        'status',
        'workflow_status',
        'accepted_at',
        'started_at',
        'completed_at',
        'follow_up_at',
        'queue_number',
        'is_emergency',
        'reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'appointment_at' => 'datetime',
            'accepted_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'follow_up_at' => 'datetime',
            'is_emergency' => 'boolean',
        ];
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class);
    }
}