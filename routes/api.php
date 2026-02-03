<?php

use App\Actions\SamplePermissionApi;
use App\Actions\SampleRoleApi;
use App\Actions\SampleUserApi;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\FlipbookHotspotController;
use App\Http\Controllers\FlipbookViewerController;
use App\Models\Flipbook;
use App\Models\FlipbookPage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Flipbook API Routes
Route::prefix('flipbooks')->group(function () {
    // Get all hotspots for a flipbook (for editor)
    Route::get('/{flipbook}/hotspots-all', function (Flipbook $flipbook) {
        $hotspots = [];

        // Page-based hotspots
        foreach ($flipbook->pages as $page) {
            foreach ($page->hotspots as $hotspot) {
                $hotspots[] = array_merge($hotspot->toArray(), [
                    'page_number' => $page->page_number
                ]);
            }
        }

        // Direct page-number hotspots (PDF.js)
        $directHotspots = \App\Models\FlipbookHotspot::where('flipbook_id', $flipbook->id)
            ->whereNotNull('page_number')
            ->get();

        foreach ($directHotspots as $hotspot) {
            $hotspots[] = $hotspot->toArray();
        }

        return response()->json($hotspots);
    });

    // Create hotspot for PDF.js flipbook
    Route::post('/{flipbook}/hotspots', function (Request $request, Flipbook $flipbook) {
        $validated = $request->validate([
            'page_number' => 'required|integer|min:1',
            'type' => 'required|in:product,link,video,popup',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'x_position' => 'required|numeric|min:0|max:100',
            'y_position' => 'required|numeric|min:0|max:100',
            'width' => 'required|numeric|min:0|max:100',
            'height' => 'required|numeric|min:0|max:100',
            'product_id' => 'nullable|exists:products,id',
            'target_url' => 'nullable|url',
            'color' => 'nullable|string',
            'animation' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['flipbook_id'] = $flipbook->id;
        $hotspot = \App\Models\FlipbookHotspot::create($validated);

        return response()->json($hotspot, 201);
    })->middleware('auth');

    // Update hotspot
    Route::put('/{flipbook}/hotspots/{hotspot}', function (Request $request, Flipbook $flipbook, \App\Models\FlipbookHotspot $hotspot) {
        $validated = $request->validate([
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'product_id' => 'nullable|exists:products,id',
            'target_url' => 'nullable|url',
            'color' => 'nullable|string',
            'animation' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $hotspot->update($validated);

        return response()->json($hotspot);
    })->middleware('auth');

    // Delete hotspot
    Route::delete('/{flipbook}/hotspots/{hotspot}', function (Flipbook $flipbook, \App\Models\FlipbookHotspot $hotspot) {
        $hotspot->delete();
        return response()->json(['message' => 'Hotspot deleted']);
    })->middleware('auth');

    // Pages
    Route::get('/pages/{page}/hotspots', [\App\Http\Controllers\Api\FlipbookApiController::class, 'getPageHotspots']);
    Route::get('/pages/{page}/hotspots/count', [\App\Http\Controllers\Api\FlipbookApiController::class, 'getPageHotspotCount']);

    // Hotspots (requires auth)
    Route::middleware('auth')->group(function () {
        Route::post('/pages/{page}/hotspots', [\App\Http\Controllers\Api\FlipbookApiController::class, 'storeHotspot']);
        Route::put('/hotspots/{hotspot}', [\App\Http\Controllers\Api\FlipbookApiController::class, 'updateHotspot']);
        Route::delete('/hotspots/{hotspot}', [\App\Http\Controllers\Api\FlipbookApiController::class, 'deleteHotspot']);
        Route::post('/hotspots/{hotspot}/duplicate', [FlipbookHotspotController::class, 'duplicate']);
        Route::patch('/hotspots/{hotspot}/toggle-active', [FlipbookHotspotController::class, 'toggleActive']);
    });

    // Products
    Route::get('/products', [\App\Http\Controllers\Api\FlipbookApiController::class, 'getProducts']);

    // Tracking (public)
    Route::post('/hotspots/{hotspot}/track', [\App\Http\Controllers\Api\FlipbookApiController::class, 'recordClick']);
});

Route::prefix('flipbook/{slug}')->group(function () {
    Route::get('/data', [FlipbookViewerController::class, 'data']);
    Route::get('/hotspots', [FlipbookViewerController::class, 'getHotspots']);
    Route::post('/track/view', [FlipbookViewerController::class, 'trackView']);
    Route::post('/track/page-turn', [FlipbookViewerController::class, 'trackPageTurn']);
    Route::post('/track/hotspot-click', [FlipbookViewerController::class, 'trackHotspotClick']);
});

// Product API Routes
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{product}', [ProductController::class, 'show']);
});

Route::prefix('v1')->group(function () {

    Route::get('/users', function (Request $request) {
        return app(SampleUserApi::class)->datatableList($request);
    });

    Route::post('/users-list', function (Request $request) {
        return app(SampleUserApi::class)->datatableList($request);
    });

    Route::post('/users', function (Request $request) {
        return app(SampleUserApi::class)->create($request);
    });

    Route::get('/users/{id}', function ($id) {
        return app(SampleUserApi::class)->get($id);
    });

    Route::put('/users/{id}', function ($id, Request $request) {
        return app(SampleUserApi::class)->update($id, $request);
    });

    Route::delete('/users/{id}', function ($id) {
        return app(SampleUserApi::class)->delete($id);
    });


    Route::get('/roles', function (Request $request) {
        return app(SampleRoleApi::class)->datatableList($request);
    });

    Route::post('/roles-list', function (Request $request) {
        return app(SampleRoleApi::class)->datatableList($request);
    });

    Route::post('/roles', function (Request $request) {
        return app(SampleRoleApi::class)->create($request);
    });

    Route::get('/roles/{id}', function ($id) {
        return app(SampleRoleApi::class)->get($id);
    });

    Route::put('/roles/{id}', function ($id, Request $request) {
        return app(SampleRoleApi::class)->update($id, $request);
    });

    Route::delete('/roles/{id}', function ($id) {
        return app(SampleRoleApi::class)->delete($id);
    });

    Route::post('/roles/{id}/users', function (Request $request, $id) {
        $request->merge(['id' => $id]);
        return app(SampleRoleApi::class)->usersDatatableList($request);
    });

    Route::delete('/roles/{id}/users/{user_id}', function ($id, $user_id) {
        return app(SampleRoleApi::class)->deleteUser($id, $user_id);
    });



    Route::get('/permissions', function (Request $request) {
        return app(SamplePermissionApi::class)->datatableList($request);
    });

    Route::post('/permissions-list', function (Request $request) {
        return app(SamplePermissionApi::class)->datatableList($request);
    });

    Route::post('/permissions', function (Request $request) {
        return app(SamplePermissionApi::class)->create($request);
    });

    Route::get('/permissions/{id}', function ($id) {
        return app(SamplePermissionApi::class)->get($id);
    });

    Route::put('/permissions/{id}', function ($id, Request $request) {
        return app(SamplePermissionApi::class)->update($id, $request);
    });

    Route::delete('/permissions/{id}', function ($id) {
        return app(SamplePermissionApi::class)->delete($id);
    });
});
