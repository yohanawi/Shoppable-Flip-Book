<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlipbookTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'preview_image',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get all flipbooks using this template
     */
    public function flipbooks()
    {
        return $this->hasMany(Flipbook::class, 'template_id');
    }

    /**
     * Scope active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
