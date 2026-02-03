<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FlipbookAnalytic extends Model
{
    use HasFactory;

    const UPDATED_AT = null; // Only track created_at

    protected $fillable = [
        'flipbook_id',
        'flipbook_page_id',
        'flipbook_hotspot_id',
        'event_type',
        'session_id',
        'user_id',
        'ip_address',
        'user_agent',
        'referrer',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    const EVENT_VIEW = 'view';
    const EVENT_PAGE_TURN = 'page_turn';
    const EVENT_HOTSPOT_CLICK = 'hotspot_click';
    const EVENT_DOWNLOAD = 'download';
    const EVENT_SHARE = 'share';

    /**
     * Get the flipbook this analytic belongs to
     */
    public function flipbook()
    {
        return $this->belongsTo(Flipbook::class);
    }

    /**
     * Get the page this analytic belongs to
     */
    public function page()
    {
        return $this->belongsTo(FlipbookPage::class, 'flipbook_page_id');
    }

    /**
     * Get the hotspot this analytic belongs to
     */
    public function hotspot()
    {
        return $this->belongsTo(FlipbookHotspot::class, 'flipbook_hotspot_id');
    }

    /**
     * Get the user who triggered this event
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an analytics event
     */
    public static function logEvent($flipbookId, $eventType, $data = [])
    {
        return static::create([
            'flipbook_id' => $flipbookId,
            'flipbook_page_id' => $data['page_id'] ?? null,
            'flipbook_hotspot_id' => $data['hotspot_id'] ?? null,
            'event_type' => $eventType,
            'session_id' => $data['session_id'] ?? session()->getId(),
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->header('referer'),
            'metadata' => $data['metadata'] ?? null,
        ]);
    }
}
