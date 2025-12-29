<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrlSetting extends Model
{
    protected $fillable = [
        'url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
