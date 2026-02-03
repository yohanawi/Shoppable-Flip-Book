<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Flipbook extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'pdf_path',
        'thumbnail',
        'template_id',
        'user_id',
        'total_pages',
        'is_published',
        'is_public',
        'settings',
        'views_count',
        'published_at',
        // New customer workflow fields
        'visibility',
        'status',
        'template_type',
        'template_config',
        'page_structure',
        'flip_physics',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_published' => 'boolean',
        'is_public' => 'boolean',
        'published_at' => 'datetime',
        'views_count' => 'integer',
        'total_pages' => 'integer',
        // New field casts
        'template_config' => 'array',
        'page_structure' => 'array',
        'flip_physics' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($flipbook) {
            if (empty($flipbook->slug)) {
                $flipbook->slug = Str::slug($flipbook->title);
            }
        });
    }

    /**
     * Get the user who created this flipbook
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the template used by this flipbook
     */
    public function template()
    {
        return $this->belongsTo(FlipbookTemplate::class);
    }

    /**
     * Get all pages of this flipbook
     */
    public function pages()
    {
        return $this->hasMany(FlipbookPage::class)->orderBy('page_number');
    }

    /**
     * Get all analytics events for this flipbook
     */
    public function analytics()
    {
        return $this->hasMany(FlipbookAnalytic::class);
    }

    /**
     * Get all hotspots across all pages
     */
    public function hotspots()
    {
        return $this->hasMany(FlipbookHotspot::class);
    }

    /**
     * Scope published flipbooks
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope public flipbooks
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope by visibility
     */
    public function scopeVisibility($query, $visibility)
    {
        return $query->where('visibility', $visibility);
    }

    /**
     * Scope by template type
     */
    public function scopeTemplateType($query, $type)
    {
        return $query->where('template_type', $type);
    }

    /**
     * Scope for customer's own flipbooks
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if flipbook is live
     */
    public function isLive()
    {
        return $this->status === 'live';
    }

    /**
     * Check if flipbook is draft
     */
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    /**
     * Check if flipbook is archived
     */
    public function isArchived()
    {
        return $this->status === 'archived';
    }

    /**
     * Increment views count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Get the public URL for this flipbook
     */
    public function getPublicUrl()
    {
        return route('flipbook.viewer', $this->slug);
    }

    /**
     * Get PDF file URL
     */
    public function getPdfUrl()
    {
        return asset('storage/' . $this->pdf_path);
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrl()
    {
        return $this->thumbnail
            ? asset('storage/' . $this->thumbnail)
            : asset('assets/media/flipbook/default-thumbnail.png');
    }
}
