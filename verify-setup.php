#!/usr/bin/env php
<?php
/**
 * Customer Workflow Setup & Test Script
 * Run this to quickly set up a test user and verify the system
 */

echo "==============================================\n";
echo "Customer Workflow - Setup & Test Script\n";
echo "==============================================\n\n";

// Check if running from project root
if (!file_exists('artisan')) {
    die("❌ Error: Please run this script from the project root directory\n");
}

echo "✅ Project root detected\n";

// Test 1: Check if roles exist
echo "\n📋 Test 1: Checking roles...\n";
exec('php artisan tinker --execute="echo Spatie\Permission\Models\Role::count();"', $output, $return);
if ($return === 0 && !empty($output)) {
    echo "✅ Roles found: " . trim(end($output)) . " roles\n";
} else {
    echo "⚠️  No roles found. Running seeder...\n";
    exec('php artisan db:seed --class=RolePermissionSeeder', $seedOutput);
    echo "✅ Roles seeded\n";
}

// Test 2: Check if Customer role exists
echo "\n📋 Test 2: Verifying Customer role...\n";
exec('php artisan tinker --execute="echo Spatie\Permission\Models\Role::where(\'name\', \'Customer\')->exists() ? \'yes\' : \'no\';"', $roleOutput);
if (trim(end($roleOutput)) === 'yes') {
    echo "✅ Customer role exists\n";
} else {
    echo "❌ Customer role not found. Please run: php artisan db:seed --class=RolePermissionSeeder\n";
}

// Test 3: Check customer routes
echo "\n📋 Test 3: Checking customer routes...\n";
exec('php artisan route:list --name=customer --json 2>/dev/null', $routeOutput, $routeReturn);
if ($routeReturn === 0) {
    $routes = json_decode(implode('', $routeOutput), true);
    if ($routes && count($routes) > 0) {
        echo "✅ Found " . count($routes) . " customer routes\n";
    } else {
        echo "⚠️  Customer routes may not be loaded properly\n";
    }
}

// Test 4: Check middleware
echo "\n📋 Test 4: Verifying middleware...\n";
if (file_exists('app/Http/Middleware/EnsureUserIsCustomer.php')) {
    echo "✅ EnsureUserIsCustomer middleware exists\n";
} else {
    echo "❌ EnsureUserIsCustomer middleware not found\n";
}

if (file_exists('app/Http/Middleware/EnsureUserIsAdmin.php')) {
    echo "✅ EnsureUserIsAdmin middleware exists\n";
} else {
    echo "❌ EnsureUserIsAdmin middleware not found\n";
}

// Test 5: Check controllers
echo "\n📋 Test 5: Verifying controllers...\n";
$controllers = [
    'CustomerDashboardController',
    'CustomerFlipbookController',
    'TemplateController',
    'PageManagementController',
    'FlipPhysicsController',
    'CustomerHotspotController',
    'CustomerSettingsController',
    'CustomerTicketController',
];

$missingControllers = [];
foreach ($controllers as $controller) {
    if (!file_exists("app/Http/Controllers/Customer/{$controller}.php")) {
        $missingControllers[] = $controller;
    }
}

if (empty($missingControllers)) {
    echo "✅ All 8 customer controllers exist\n";
} else {
    echo "❌ Missing controllers: " . implode(', ', $missingControllers) . "\n";
}

// Test 6: Check views
echo "\n📋 Test 6: Checking views...\n";
$views = [
    'customer/dashboard.blade.php',
    'customer/catalog/index.blade.php',
    'customer/catalog/create.blade.php',
    'customer/templates/slicer.blade.php',
];

$existingViews = 0;
foreach ($views as $view) {
    if (file_exists("resources/views/{$view}")) {
        $existingViews++;
    }
}

echo "✅ Core views: {$existingViews}/4 created\n";

// Test 7: Check policy
echo "\n📋 Test 7: Verifying policy...\n";
if (file_exists('app/Policies/FlipbookPolicy.php')) {
    echo "✅ FlipbookPolicy exists\n";
} else {
    echo "❌ FlipbookPolicy not found\n";
}

// Test 8: Check migrations
echo "\n📋 Test 8: Checking migrations...\n";
exec('php artisan migrate:status 2>/dev/null', $migrateOutput);
$customerMigrations = array_filter($migrateOutput, function ($line) {
    return strpos($line, 'customer') !== false ||
        strpos($line, 'template') !== false ||
        strpos($line, 'enhance_hotspot') !== false;
});

if (count($customerMigrations) >= 3) {
    echo "✅ Customer workflow migrations detected\n";
} else {
    echo "⚠️  Run migrations: php artisan migrate\n";
}

// Summary
echo "\n==============================================\n";
echo "SUMMARY\n";
echo "==============================================\n\n";

// Quick setup instructions
echo "🚀 QUICK SETUP:\n\n";
echo "1. Assign Customer role to a user:\n";
echo "   php artisan tinker\n";
echo "   >>> \$user = User::find(1);\n";
echo "   >>> \$user->assignRole('Customer');\n";
echo "   >>> exit\n\n";

echo "2. Start the server (if not running):\n";
echo "   php artisan serve\n\n";

echo "3. Login and visit:\n";
echo "   http://127.0.0.1:8000/customer/dashboard\n\n";

echo "4. Create your first flipbook:\n";
echo "   - Click 'Create FlipBook'\n";
echo "   - Upload PDF\n";
echo "   - Choose 'Slicer' template\n";
echo "   - Add hotspots by clicking & dragging\n";
echo "   - Click 'Save & Publish'\n\n";

echo "==============================================\n";
echo "📚 Documentation:\n";
echo "   - CUSTOMER_WORKFLOW_COMPLETE.md\n";
echo "   - QUICK_START.md\n";
echo "   - CUSTOMER_WORKFLOW_IMPLEMENTATION.md\n";
echo "==============================================\n\n";

echo "✅ Setup verification complete!\n";
