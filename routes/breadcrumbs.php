<?php

use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Spatie\Permission\Models\Role;

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('dashboard'));
});

// Home > Dashboard
Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Dashboard', route('dashboard'));
});

// Home > Dashboard > User Management
Breadcrumbs::for('user-management.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('User Management', route('user-management.users.index'));
});

// Home > Dashboard > User Management > Users
Breadcrumbs::for('user-management.users.index', function (BreadcrumbTrail $trail) {
    $trail->parent('user-management.index');
    $trail->push('Users', route('user-management.users.index'));
});

// Home > Dashboard > User Management > Users > [User]
Breadcrumbs::for('user-management.users.show', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('user-management.users.index');
    $trail->push(ucwords($user->name), route('user-management.users.show', $user));
});

// Home > Dashboard > User Management > Roles
Breadcrumbs::for('user-management.roles.index', function (BreadcrumbTrail $trail) {
    $trail->parent('user-management.index');
    $trail->push('Roles', route('user-management.roles.index'));
});

// Home > Dashboard > User Management > Roles > [Role]
Breadcrumbs::for('user-management.roles.show', function (BreadcrumbTrail $trail, Role $role) {
    $trail->parent('user-management.roles.index');
    $trail->push(ucwords($role->name), route('user-management.roles.show', $role));
});

// Home > Dashboard > User Management > Permission
Breadcrumbs::for('user-management.permissions.index', function (BreadcrumbTrail $trail) {
    $trail->parent('user-management.index');
    $trail->push('Permissions', route('user-management.permissions.index'));
});

// Home > Dashboard > Tickets
Breadcrumbs::for('customer.tickets.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Support Tickets', route('customer.tickets.index'));
});

// Home > Dashboard > Tickets > Create
Breadcrumbs::for('customer.tickets.create', function (BreadcrumbTrail $trail) {
    $trail->parent('customer.tickets.index');
    $trail->push('Create Ticket', route('customer.tickets.create'));
});

// Home > Dashboard > Tickets > [Ticket]
Breadcrumbs::for('customer.tickets.show', function (BreadcrumbTrail $trail, $ticket) {
    $trail->parent('customer.tickets.index');
    $trail->push('Ticket #' . $ticket->id, route('customer.tickets.show', $ticket));
});

// Hoem > Dashboard > Tickets > Admin > Index
Breadcrumbs::for('admin.tickets.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('All Tickets', route('admin.tickets.index'));
});

// Home > Dashboard > Tickets > Admin > [Ticket]
Breadcrumbs::for('admin.tickets.show', function (BreadcrumbTrail $trail, $ticket) {
    $trail->parent('admin.tickets.index');
    $trail->push('Ticket #' . $ticket->id, route('admin.tickets.show', $ticket));
});

// Home > Dashboard > Settings
Breadcrumbs::for('customer.settings.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Customer Settings', route('customer.settings.index'));
});

// Home > Dashboard > Catalog
Breadcrumbs::for('customer.catalog.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('My Catalog', route('customer.catalog.index'));
});

// Home > Dashboard > Catalog > Create
Breadcrumbs::for('customer.catalog.create', function (BreadcrumbTrail $trail) {
    $trail->parent('customer.catalog.index');
    $trail->push('Create', route('customer.catalog.create'));
});

// Home > Dashboard > Catalog > Edit
Breadcrumbs::for('customer.catalog.edit', function (BreadcrumbTrail $trail, $flipbook) {
    $trail->parent('customer.catalog.index');
    $trail->push('Edit', route('customer.catalog.edit', $flipbook));
});
