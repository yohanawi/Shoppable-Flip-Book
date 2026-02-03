<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlipbookHotspot extends Model
{
    use HasFactory;

    protected $fillable = [
        'flipbook_id',
        'flipbook_page_id',
        'page_number',
        'type',
        'shape_type',
        'coordinates',
        'action_type',
        'target_page_number',
        'popup_media_url',
        'popup_type',
        'title',
        'description',
        'x_position',
        'y_position',
        'width',
        'height',
        'target_url',
        'target_route',
        'target_params',
        'product_id',
        'product_name',
        'popup_content',
        'icon',
        'color',
        'animation',
        'is_active',
        'target_type',
        'display_order',
    ];

    protected $casts = [
        'target_params' => 'array',
        'coordinates' => 'array',
        'is_active' => 'boolean',
        'x_position' => 'decimal:4',
        'y_position' => 'decimal:4',
        'width' => 'decimal:4',
        'height' => 'decimal:4',
        'display_order' => 'integer',
        'target_page_number' => 'integer',
    ];

    const TYPE_LINK = 'link';
    const TYPE_PRODUCT = 'product';
    const TYPE_VIDEO = 'video';
    const TYPE_POPUP = 'popup';
    const TYPE_INTERNAL = 'internal';
    const TYPE_EXTERNAL = 'external';

    const TARGET_BLANK = '_blank';
    const TARGET_SELF = '_self';
    const TARGET_MODAL = 'modal';
    const TARGET_CART = 'cart';

    /**
     * Get the page this hotspot belongs to
     */
    public function page()
    {
        return $this->belongsTo(FlipbookPage::class, 'flipbook_page_id');
    }

    /**
     * Get analytics for this hotspot
     */
    public function analytics()
    {
        return $this->hasMany(FlipbookAnalytic::class);
    }

    /**
     * Scope active hotspots
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the target URL for this hotspot
     */
    public function getTargetUrl()
    {
        if ($this->type === self::TYPE_EXTERNAL && $this->target_url) {
            return $this->target_url;
        }

        if ($this->type === self::TYPE_INTERNAL && $this->target_route) {
            try {
                return route($this->target_route, $this->target_params ?? []);
            } catch (\Exception $e) {
                return '#';
            }
        }

        if ($this->type === self::TYPE_PRODUCT && $this->product_id) {
            // Return API URL for products
            return url("/api/products/{$this->product_id}");
        }

        if ($this->type === self::TYPE_LINK && $this->target_url) {
            return $this->target_url;
        }

        return '#';
    }

    /**
     * Get CSS style for positioning
     */
    public function getStyleAttribute()
    {
        return sprintf(
            'left: %s%%; top: %s%%; width: %s%%; height: %s%%;',
            $this->x_position,
            $this->y_position,
            $this->width,
            $this->height
        );
    }

    /**
     * Track click event
     */
    public function trackClick($sessionId = null, $userId = null)
    {
        FlipbookAnalytic::create([
            'flipbook_id' => $this->page->flipbook_id,
            'flipbook_page_id' => $this->flipbook_page_id,
            'flipbook_hotspot_id' => $this->id,
            'event_type' => 'hotspot_click',
            'session_id' => $sessionId,
            'user_id' => $userId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
