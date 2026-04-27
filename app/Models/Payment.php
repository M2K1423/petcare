<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'appointment_id',
        'medicine_order_id',
        'owner_id',
        'service_id',
        'amount',
        'payment_method',
        'gateway',
        'status',
        'paid_at',
        'transaction_code',
        'gateway_transaction_no',
        'gateway_response_code',
        'gateway_payload',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'gateway_payload' => 'array',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function medicineOrder(): BelongsTo
    {
        return $this->belongsTo(MedicineOrder::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
