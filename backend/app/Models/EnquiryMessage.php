<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnquiryMessage extends Model
{
    protected $fillable = [
        'enquiry_id', 'user_id', 'sender_type', 'sender_name', 'message', 'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
