<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// ashraf29122025 : database model for storing activity logs
class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'location',
        'scope',
    ];

    protected $casts = [
        'location' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
