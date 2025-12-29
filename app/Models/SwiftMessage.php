<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SwiftMessage extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'messages' => 'array', // Automatically cast JSON to array
        'system_datime' => 'datetime',
    ];
}
