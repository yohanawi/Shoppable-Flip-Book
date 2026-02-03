<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlipbookPhysicsPreset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_default',
        'parameters',
    ];

    protected $casts = [
        'parameters' => 'array',
        'is_default' => 'boolean',
    ];

    /**
     * Get the default preset
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Get all presets for dropdown
     */
    public static function getAllForSelect()
    {
        return static::orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id');
    }
}
