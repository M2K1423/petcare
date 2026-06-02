<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class OwnerCart extends Model
{
    protected $table = 'owner_carts';

    protected $fillable = [
        'owner_id',
        'items',
    ];

    protected $casts = [
        'items' => 'array',
    ];
}
