<?php

use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\Apps\PermissionManagementController;
use App\Http\Controllers\Apps\RoleManagementController;
use App\Http\Controllers\Apps\UserManagementController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Customer\CustomerSettingsController;
use App\Http\Controllers\Customer\CustomerTicketController;
use App\Http\Controllers\Customer\CustomerTwoFactorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FlipbookController;
use App\Http\Controllers\FlipbookHotspotController;
use App\Http\Controllers\FlipbookViewerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', [DashboardController::class, 'index']);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Administrator Only Routes
    Route::middleware(['administrator'])->group(function () {
        Route::name('user-management.')->group(function () {
            Route::resource('/user-management/users', UserManagementController::class);
            Route::resource('/user-management/roles', RoleManagementController::class);
            Route::resource('/user-management/permissions', PermissionManagementController::class);
        });

        // Flipbook Management Routes (Admin)
        Route::prefix('flipbooks')->name('flipbooks.')->group(function () {
            Route::get('/', [FlipbookController::class, 'index'])->name('index');
            Route::get('/create', [FlipbookController::class, 'create'])->name('create');
            Route::post('/', [FlipbookController::class, 'store'])->name('store');
            Route::get('/{flipbook}/edit', [FlipbookController::class, 'edit'])->name('edit');
            Route::put('/{flipbook}', [FlipbookController::class, 'update'])->name('update');
            Route::delete('/{flipbook}', [FlipbookController::class, 'destroy'])->name('destroy');
            Route::get('/{flipbook}/editor', [FlipbookController::class, 'editor'])->name('editor');
            Route::patch('/{flipbook}/toggle-publish', [FlipbookController::class, 'togglePublish'])->name('toggle-publish');
            Route::get('/{flipbook}/analytics', [FlipbookViewerController::class, 'analytics'])->name('analytics');

            // Editor API routes (use web middleware for session auth)
            Route::get('/{flipbook}/hotspots-all', function (\App\Models\Flipbook $flipbook) {
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
            })->name('hotspots.all');

            Route::post('/{flipbook}/hotspots', function (\Illuminate\Http\Request $request, \App\Models\Flipbook $flipbook) {
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
                    'product_name' => 'nullable|string',
                    'target_url' => 'nullable|url',
                    'color' => 'nullable|string',
                    'animation' => 'nullable|string',
                    'is_active' => 'nullable',
                ]);

                // Convert is_active to boolean
                $validated['is_active'] = filter_var($validated['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
                $validated['flipbook_id'] = $flipbook->id;

                $hotspot = \App\Models\FlipbookHotspot::create($validated);

                return response()->json($hotspot, 201);
            })->name('hotspots.store');

            Route::put('/{flipbook}/hotspots/{hotspot}', function (\Illuminate\Http\Request $request, \App\Models\Flipbook $flipbook, \App\Models\FlipbookHotspot $hotspot) {
                $validated = $request->validate([
                    'title' => 'nullable|string',
                    'description' => 'nullable|string',
                    'product_id' => 'nullable|exists:products,id',
                    'product_name' => 'nullable|string',
                    'target_url' => 'nullable|url',
                    'color' => 'nullable|string',
                    'animation' => 'nullable|string',
                    'is_active' => 'nullable',
                ]);

                // Convert is_active to boolean if present
                if (isset($validated['is_active'])) {
                    $validated['is_active'] = filter_var($validated['is_active'], FILTER_VALIDATE_BOOLEAN);
                }

                $hotspot->update($validated);

                return response()->json($hotspot);
            })->name('hotspots.update');

            Route::delete('/{flipbook}/hotspots/{hotspot}', function (\App\Models\Flipbook $flipbook, \App\Models\FlipbookHotspot $hotspot) {
                $hotspot->delete();
                return response()->json(['message' => 'Hotspot deleted']);
            })->name('hotspots.destroy');
        });
    });

    // Customer Routes
    Route::middleware(['customer'])->prefix('customer')->name('customer.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Customer\CustomerDashboardController::class, 'index'])->name('dashboard');

        // Catalog Management
        Route::prefix('catalog')->name('catalog.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Customer\CustomerFlipbookController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Customer\CustomerFlipbookController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Customer\CustomerFlipbookController::class, 'store'])->name('store');
            Route::get('/{flipbook}', [\App\Http\Controllers\Customer\CustomerFlipbookController::class, 'show'])->name('show');
            Route::get('/{flipbook}/edit', [\App\Http\Controllers\Customer\CustomerFlipbookController::class, 'edit'])->name('edit');
            Route::put('/{flipbook}', [\App\Http\Controllers\Customer\CustomerFlipbookController::class, 'update'])->name('update');
            Route::delete('/{flipbook}', [\App\Http\Controllers\Customer\CustomerFlipbookController::class, 'destroy'])->name('destroy');
            Route::post('/{flipbook}/publish', [\App\Http\Controllers\Customer\CustomerFlipbookController::class, 'publish'])->name('publish');
            Route::post('/{flipbook}/unpublish', [\App\Http\Controllers\Customer\CustomerFlipbookController::class, 'unpublish'])->name('unpublish');
            Route::get('/{flipbook}/preview', [\App\Http\Controllers\Customer\CustomerFlipbookController::class, 'preview'])->name('preview');
            Route::post('/{flipbook}/duplicate', [\App\Http\Controllers\Customer\CustomerFlipbookController::class, 'duplicate'])->name('duplicate');
        });

        // Template Configuration
        Route::prefix('template')->name('template.')->group(function () {
            Route::get('/{flipbook}/{type}', [\App\Http\Controllers\Customer\TemplateController::class, 'show'])->name('show');
            Route::post('/{flipbook}/config', [\App\Http\Controllers\Customer\TemplateController::class, 'saveConfig'])->name('save-config');
        });

        // Page Management
        Route::prefix('pages')->name('pages.')->group(function () {
            Route::get('/{flipbook}', [\App\Http\Controllers\Customer\CustomerPageManagementController::class, 'index'])->name('index');
            Route::get('/{flipbook}/data', [\App\Http\Controllers\Customer\CustomerPageManagementController::class, 'getPages'])->name('data');
            Route::post('/{flipbook}/reorder', [\App\Http\Controllers\Customer\CustomerPageManagementController::class, 'reorder'])->name('reorder');
            Route::put('/{flipbook}/{pageId}/rename', [\App\Http\Controllers\Customer\CustomerPageManagementController::class, 'rename'])->name('rename');
            Route::delete('/{flipbook}/{pageId}', [\App\Http\Controllers\Customer\CustomerPageManagementController::class, 'delete'])->name('delete');
            Route::patch('/{flipbook}/{pageId}/toggle-lock', [\App\Http\Controllers\Customer\CustomerPageManagementController::class, 'toggleLock'])->name('toggle-lock');
            Route::patch('/{flipbook}/{pageId}/toggle-visibility', [\App\Http\Controllers\Customer\CustomerPageManagementController::class, 'toggleVisibility'])->name('toggle-visibility');
            Route::post('/{flipbook}/bulk', [\App\Http\Controllers\Customer\CustomerPageManagementController::class, 'bulkOperation'])->name('bulk');
        });

        // Flip Physics
        Route::prefix('physics')->name('physics.')->group(function () {
            Route::get('/{flipbook}', [\App\Http\Controllers\Customer\CustomerFlipPhysicsController::class, 'index'])->name('index');
            Route::get('/presets/all', [\App\Http\Controllers\Customer\CustomerFlipPhysicsController::class, 'getPresets'])->name('presets.all');
            Route::get('/presets/{presetId}', [\App\Http\Controllers\Customer\CustomerFlipPhysicsController::class, 'getPreset'])->name('presets.show');
            Route::post('/{flipbook}/save', [\App\Http\Controllers\Customer\CustomerFlipPhysicsController::class, 'save'])->name('save');
            Route::post('/{flipbook}/apply-preset', [\App\Http\Controllers\Customer\CustomerFlipPhysicsController::class, 'applyPreset'])->name('apply-preset');
            Route::post('/{flipbook}/reset', [\App\Http\Controllers\Customer\CustomerFlipPhysicsController::class, 'reset'])->name('reset');
        });

        // Enhanced Hotspots (Slicer)
        Route::prefix('hotspots')->name('hotspots.')->group(function () {
            Route::get('/{flipbook}', [\App\Http\Controllers\Customer\CustomerHotspotController::class, 'index'])->name('index');
            Route::get('/{flipbook}/data', [\App\Http\Controllers\Customer\CustomerHotspotController::class, 'getHotspots'])->name('data');
            Route::post('/{flipbook}', [\App\Http\Controllers\Customer\CustomerHotspotController::class, 'store'])->name('store');
            Route::put('/{flipbook}/{hotspot}', [\App\Http\Controllers\Customer\CustomerHotspotController::class, 'update'])->name('update');
            Route::delete('/{flipbook}/{hotspot}', [\App\Http\Controllers\Customer\CustomerHotspotController::class, 'destroy'])->name('destroy');
            Route::post('/{flipbook}/bulk-delete', [\App\Http\Controllers\Customer\CustomerHotspotController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('/{flipbook}/upload-media', [\App\Http\Controllers\Customer\CustomerHotspotController::class, 'uploadMedia'])->name('upload-media');
            Route::post('/{flipbook}/{hotspot}/duplicate', [\App\Http\Controllers\Customer\CustomerHotspotController::class, 'duplicate'])->name('duplicate');
        });

        // Account Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [CustomerSettingsController::class, 'index'])->name('index');
            Route::put('/profile', [CustomerSettingsController::class, 'updateProfile'])->name('update-profile');
            Route::put('/password', [CustomerSettingsController::class, 'updatePassword'])->name('update-password');
        });

        // Support Tickets
        Route::prefix('tickets')->name('tickets.')->group(function () {
            Route::get('/', [CustomerTicketController::class, 'index'])->name('index');
            Route::get('/create', [CustomerTicketController::class, 'create'])->name('create');
            Route::post('/', [CustomerTicketController::class, 'store'])->name('store');
            Route::get('/{ticket}', [CustomerTicketController::class, 'show'])->name('show');
            Route::post('/{ticket}/reply', [CustomerTicketController::class, 'reply'])->name('reply');
        });
    });

    Route::middleware(['auth', 'admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::prefix('tickets')->name('tickets.')->group(function () {
                Route::get('/', [AdminTicketController::class, 'index'])->name('index');
                Route::get('/{ticket}', [AdminTicketController::class, 'show'])->name('show');
                Route::post('/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('reply');
            });
        });
});

Route::get('/error', function () {
    abort(500);
});

// Flipbook System Test Page
Route::get('/flipbook-test', function () {
    return view('flipbooks.test');
})->name('flipbook.test');

// Debug route to test PDF access
Route::get('/flipbook-debug/{slug}', function ($slug) {
    $flipbook = \App\Models\Flipbook::where('slug', $slug)->firstOrFail();
    $pdfPath = storage_path('app/public/' . $flipbook->pdf_path);
    $publicPath = public_path('storage/' . $flipbook->pdf_path);

    return response()->json([
        'flipbook' => $flipbook->only(['id', 'title', 'slug', 'pdf_path', 'total_pages']),
        'pdf_path_db' => $flipbook->pdf_path,
        'full_storage_path' => $pdfPath,
        'storage_file_exists' => file_exists($pdfPath),
        'storage_file_size' => file_exists($pdfPath) ? filesize($pdfPath) : 0,
        'public_symlink_path' => $publicPath,
        'public_file_exists' => file_exists($publicPath),
        'asset_url' => asset('storage/' . $flipbook->pdf_path),
        'url_url' => url('storage/' . $flipbook->pdf_path),
        'storage_url' => \Storage::disk('public')->url($flipbook->pdf_path),
    ]);
})->name('flipbook.debug');

Route::get('/auth/redirect/{provider}', [SocialiteController::class, 'redirect']);

// Public Flipbook Viewer Routes
Route::prefix('flipbook')->name('flipbook.')->group(function () {
    Route::get('/{slug}', [FlipbookViewerController::class, 'show'])->name('viewer');
    Route::get('/{slug}/data', [FlipbookViewerController::class, 'data'])->name('data');
    Route::get('/{slug}/download', [FlipbookViewerController::class, 'download'])->name('download');
});

// Shopping Cart Routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::put('/{cartItem}', [CartController::class, 'update'])->name('update');
    Route::delete('/{cartItem}', [CartController::class, 'remove'])->name('remove');
    Route::get('/count', [CartController::class, 'count'])->name('count');
});

require __DIR__ . '/auth.php';
