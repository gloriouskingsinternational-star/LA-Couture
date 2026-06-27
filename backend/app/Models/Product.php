<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'sku', 'name', 'category', 'price',
        'description', 'image', 'tag', 'active', 'stock',
    ];

    protected $casts = [
        'price'  => 'decimal:2',
        'active' => 'boolean',
        'stock'  => 'integer',
    ];
}
