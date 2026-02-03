<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Flipbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
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

        return view('customer.dashboard', compact(
            'totalFlipbooks',
            'publishedFlipbooks',
            'draftFlipbooks',
            'recentFlipbooks'
        ));
    }
}
