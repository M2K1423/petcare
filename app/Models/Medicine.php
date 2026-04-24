<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicine extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'unit',
        'stock_quantity',
        'price',
        'expiration_date',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'expiration_date' => 'date',
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(MedicineOrderItem::class);
    }
}
