<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactInquiry extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'inquiry_type',
        'service_interest',
        'preferred_date',
        'message',
    ];

    protected $casts = [
        'preferred_date' => 'date',
    ];
}
