<?php

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\AgentAuthController;
use App\Http\Controllers\Auth\ClientAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Agent\DashboardController as AgentDashboardController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to appropriate login
Route::get('/', function () {
    return redirect('/client/login');
});

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'create'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'store']);
    Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('admin.logout');
});

// Agent Authentication Routes
Route::prefix('agent')->group(function () {
    Route::get('/login', [AgentAuthController::class, 'create'])->name('agent.login');
    Route::post('/login', [AgentAuthController::class, 'store']);
    Route::post('/logout', [AgentAuthController::class, 'destroy'])->name('agent.logout');
});

// Client Authentication Routes
Route::prefix('client')->group(function () {
    Route::get('/login', [ClientAuthController::class, 'create'])->name('client.login');
    Route::post('/login', [ClientAuthController::class, 'store']);
    Route::get('/register', [ClientAuthController::class, 'showRegistrationForm'])->name('client.register');
    Route::post('/register', [ClientAuthController::class, 'register']);
    Route::post('/logout', [ClientAuthController::class, 'destroy'])->name('client.logout');
});

// Admin Protected Routes
Route::middleware(['admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    // User Management
    Route::resource('users', UserController::class)->names('admin.users');
    
    // Category Management
    Route::resource('categories', CategoryController::class)->names('admin.categories');
    
    // Ticket Management
    Route::get('/tickets', [TicketController::class, 'adminIndex'])->name('admin.tickets.index');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('admin.tickets.show');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('admin.tickets.update');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('admin.tickets.destroy');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('admin.reports.export');
});

// Agent Protected Routes
Route::middleware(['agent'])->prefix('agent')->group(function () {
    Route::get('/dashboard', [AgentDashboardController::class, 'index'])->name('agent.dashboard');
    
    // Ticket Management
    Route::get('/tickets', [TicketController::class, 'agentIndex'])->name('agent.tickets.index');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('agent.tickets.show');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('agent.tickets.update');
    
    // Profile
    Route::get('/profile', function () {
        return Inertia::render('Agent/Profile');
    })->name('agent.profile');
    Route::post('/availability', [AgentDashboardController::class, 'updateAvailability'])->name('agent.availability');
});

// Client Protected Routes
Route::middleware(['client'])->prefix('client')->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('client.dashboard');
    
    // Ticket Management
    Route::get('/tickets', [TicketController::class, 'clientIndex'])->name('client.tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('client.tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('client.tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('client.tickets.show');
    
    // Profile
    Route::get('/profile', [ClientDashboardController::class, 'profile'])->name('client.profile');
    Route::put('/profile', [ClientDashboardController::class, 'updateProfile'])->name('client.profile.update');
});

// Common authenticated routes (for all logged-in users)
Route::middleware(['auth:admin,agent,client'])->group(function () {
    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    });
    
    // Chatify routes handled by package
});

require __DIR__.'/auth.php';