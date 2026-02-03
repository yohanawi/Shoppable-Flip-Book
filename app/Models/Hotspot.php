<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotspot extends Model
{
    use HasFactory;

    protected $fillable = [
        'flipbook_page_id',
        'type',
        'title',
        'description',
        'x',
        'y',
        'width',
        'height',
        'target_url',
        'product_id',
        'target_page',
        'icon',
        'color',
        'opacity',
        'clicks',
        'is_active',
    ];

    protected $casts = [
        'x' => 'decimal:4',
        'y' => 'decimal:4',
        'width' => 'decimal:4',
        'height' => 'decimal:4',
        'opacity' => 'decimal:2',
        'clicks' => 'integer',
        'target_page' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the page that owns the hotspot.
     */
    public function page()
    {
        return $this->belongsTo(FlipbookPage::class, 'flipbook_page_id');
    }

    /**
     * Get the product associated with the hotspot.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Increment the click count.
     */
    public function recordClick()
    {
        $this->increment('clicks');
    }

    /**
     * Get the action URL based on hotspot type.
     */
    public function getActionUrlAttribute()
    {
        return match ($this->type) {
            'product' => $this->product ? route('products.show', $this->product_id) : null,
            'link' => $this->target_url,
            'internal' => null, // Handled by JS
            default => $this->target_url,
        };
    }

    /**
     * Scope to filter active hotspots.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
