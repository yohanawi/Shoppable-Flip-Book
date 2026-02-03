<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlipbookPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'flipbook_id',
        'page_number',
        'custom_name',
        'display_order',
        'is_locked',
        'is_hidden',
        'image_path',
        'thumbnail_path',
        'width',
        'height',
        'text_content',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'width' => 'integer',
        'height' => 'integer',
        'page_number' => 'integer',
        'display_order' => 'integer',
        'is_locked' => 'boolean',
        'is_hidden' => 'boolean',
    ];

    /**
     * Get the flipbook this page belongs to
     */
    public function flipbook()
    {
        return $this->belongsTo(Flipbook::class);
    }

    /**
     * Get all hotspots on this page
     */
    public function hotspots()
    {
        return $this->hasMany(FlipbookHotspot::class)->orderBy('display_order');
    }

    /**
     * Get active hotspots only
     */
    public function activeHotspots()
    {
        return $this->hasMany(FlipbookHotspot::class)->where('is_active', true)->orderBy('display_order');
    }

    /**
     * Get analytics for this page
     */
    public function analytics()
    {
        return $this->hasMany(FlipbookAnalytic::class);
    }

    /**
     * Get the image URL
     */
    public function getImageUrl()
    {
        return asset('storage/' . $this->image_path);
    }

    /**
     * Get the thumbnail URL
     */
    public function getThumbnailUrl()
    {
        return $this->thumbnail_path
            ? asset('storage/' . $this->thumbnail_path)
            : $this->getImageUrl();
    }

    /**
     * Get next page
     */
    public function nextPage()
    {
        return static::where('flipbook_id', $this->flipbook_id)
            ->where('page_number', '>', $this->page_number)
            ->orderBy('page_number')
            ->first();
    }

    /**
     * Get previous page
     */
    public function previousPage()
    {
        return static::where('flipbook_id', $this->flipbook_id)
            ->where('page_number', '<', $this->page_number)
            ->orderByDesc('page_number')
            ->first();
    }
}
