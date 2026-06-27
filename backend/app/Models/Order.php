<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'reference', 'user_id', 'client_name', 'client_email',
        'client_phone', 'total', 'status', 'whatsapp_ref',
        'paid_at', 'delivered_at', 'notes',
    ];

    protected $casts = [
        'total'        => 'decimal:2',
        'paid_at'      => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
