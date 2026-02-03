<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== USERS ===" . PHP_EOL;
$users = App\Models\User::all(['id', 'name', 'email', 'role']);
foreach ($users as $user) {
    echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email} | Role: {$user->role}" . PHP_EOL;
}

echo PHP_EOL . "=== ROLES ===" . PHP_EOL;
$roles = Spatie\Permission\Models\Role::all(['id', 'name']);
foreach ($roles as $role) {
    echo "ID: {$role->id} | Name: {$role->name}" . PHP_EOL;
    $permissions = $role->permissions->pluck('name')->take(5);
    echo "  First 5 Permissions: " . $permissions->implode(', ') . PHP_EOL;
}

echo PHP_EOL . "=== TOTAL PERMISSIONS ===" . PHP_EOL;
$totalPermissions = Spatie\Permission\Models\Permission::count();
echo "Total Permissions Created: {$totalPermissions}" . PHP_EOL;
