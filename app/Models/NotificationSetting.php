<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'mailer',
        'scheme',
        'email',
        'from_address',
        'password',
        'host',
        'port',
        'username',
        'encryption',
        'from_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'password' => 'encrypted',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->is_active) {
                // Deactivate all other settings
                static::where('id', '!=', $model->id)->update(['is_active' => false]);
            }
        });
    }
}
