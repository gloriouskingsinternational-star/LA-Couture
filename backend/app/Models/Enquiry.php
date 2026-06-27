<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $fillable = [
        'user_id', 'client_name', 'client_email',
        'subject', 'category', 'description',
        'status', 'order_reference',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(EnquiryMessage::class)->orderBy('created_at');
    }
}
