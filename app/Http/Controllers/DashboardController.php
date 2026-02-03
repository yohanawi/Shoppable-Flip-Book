<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Flipbook;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get stats
        $totalFlipbooks = Flipbook::where('user_id', $user->id)->count();
        $publishedFlipbooks = Flipbook::where('user_id', $user->id)
            ->where('status', 'live')
            ->count();
        $draftFlipbooks = Flipbook::where('user_id', $user->id)
            ->where('status', 'draft')
            ->count();

        // Get recent flipbooks
        $recentFlipbooks = Flipbook::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('pages.dashboards.index', compact(
            'totalFlipbooks',
            'publishedFlipbooks',
            'draftFlipbooks',
            'recentFlipbooks'
        ));
    }
}
