<?php

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\AgentAuthController;
use App\Http\Controllers\Auth\ClientAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Agent\DashboardController as AgentDashboardController;
use App\Http\Controllers\Agent\ProfileController as AgentProfileController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketChatController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ChatifyController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

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
    Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('admin.tickets.edit');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('admin.tickets.update');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('admin.tickets.destroy');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('admin.reports.export');
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications');
    
    // Profile Routes
    Route::get('/profile', [AdminProfileController::class, 'show'])->name('admin.profile');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
});

// Agent Protected Routes
Route::middleware(['agent'])->prefix('agent')->group(function () {
    Route::get('/dashboard', [AgentDashboardController::class, 'index'])->name('agent.dashboard');
    
    // Ticket Management
    Route::get('/tickets', [TicketController::class, 'agentIndex'])->name('agent.tickets.index');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('agent.tickets.show');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('agent.tickets.update');
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('agent.notifications');
    
    // Profile Routes
    Route::get('/profile', [AgentProfileController::class, 'show'])->name('agent.profile');
    Route::put('/profile', [AgentProfileController::class, 'update'])->name('agent.profile.update');
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
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('client.notifications');
    
    // Profile Routes
    Route::get('/profile', [ClientDashboardController::class, 'profile'])->name('client.profile');
    Route::put('/profile', [ClientDashboardController::class, 'updateProfile'])->name('client.profile.update');
});

// Common authenticated routes (for all logged-in users)
Route::middleware(['multi.auth'])->group(function () {
    // AJAX Notification endpoints
    Route::prefix('notifications')->group(function () {
        Route::get('/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::get('/preferences', [NotificationController::class, 'preferences'])->name('notifications.preferences');
        Route::put('/preferences', [NotificationController::class, 'updatePreferences'])->name('notifications.update-preferences');
    });

    // Ticket Chat API Routes
    Route::prefix('api/tickets/{ticket}/chat')->group(function () {
        Route::get('/status', [TicketChatController::class, 'getChatStatus'])->name('ticket.chat.status');
        Route::get('/messages', [TicketChatController::class, 'getMessages'])->name('ticket.chat.messages');
        Route::post('/send', [TicketChatController::class, 'sendMessage'])->name('ticket.chat.send');
        Route::post('/mark-seen', [TicketChatController::class, 'markAsSeen'])->name('ticket.chat.mark-seen');
    });

    // Custom Chatify routes for multi-guard support (if you still want the full Chatify interface)
    Route::prefix('chatify')->group(function () {
        Route::get('/{id?}', [ChatifyController::class, 'index'])->name('chatify');
        Route::post('/api/send-message', [ChatifyController::class, 'send'])->name('chatify.send');
        Route::post('/api/make-seen', [ChatifyController::class, 'seen'])->name('chatify.seen');
        Route::get('/api/fetch/{id}', [ChatifyController::class, 'fetch'])->name('chatify.fetch');
        Route::post('/api/send-attachment', [ChatifyController::class, 'sendAttachment'])->name('chatify.attachment');
        Route::get('/api/download/{fileName}', [ChatifyController::class, 'download'])->name('chatify.download');
        Route::post('/api/contacts', [ChatifyController::class, 'getContacts'])->name('chatify.contacts');
        Route::post('/api/update-contact-item', [ChatifyController::class, 'updateContactItem'])->name('chatify.update');
        Route::post('/api/favorite', [ChatifyController::class, 'favorite'])->name('chatify.favorite');
        Route::post('/api/get-favorites', [ChatifyController::class, 'getFavorites'])->name('chatify.favorites');
        Route::post('/api/search', [ChatifyController::class, 'search'])->name('chatify.search');
        Route::post('/api/shared-photos', [ChatifyController::class, 'sharedPhotos'])->name('chatify.photos');
        Route::post('/api/delete-conversation', [ChatifyController::class, 'deleteConversation'])->name('chatify.delete');
        Route::post('/api/delete-message', [ChatifyController::class, 'deleteMessage'])->name('chatify.deleteMessage');
    });
});

Route::get('/test-pdf', function () {
    $html = '<h1>Test PDF</h1><p>This is a test PDF to check if DomPDF is working.</p>';
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
    return $pdf->download('test.pdf');
});

// Route::get('/debug-pdf', function () {
//     try {
//         // Get some sample tickets
//         $tickets = \App\Models\Ticket::with(['client.user', 'agent.user', 'category'])
//             ->take(5)
//             ->get();
            
//         $stats = [
//             'total_tickets' => $tickets->count(),
//             'resolved_tickets' => $tickets->where('status', 'Resolved')->count(),
//             'pending_tickets' => $tickets->whereIn('status', ['Open', 'In Progress'])->count(),
//             'period' => 'Debug Test',
//         ];
        
//         Log::info('Debug PDF - Tickets: ' . $tickets->count());
//         Log::info('Debug PDF - Stats: ' . json_encode($stats));
        
//         // Generate PDF directly
//         $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.tickets-pdf', compact('tickets', 'stats'));
//         $pdf->setPaper('A4', 'landscape');
//         $pdf->setOptions([
//             'isHtml5ParserEnabled' => true,
//             'isRemoteEnabled' => false,
//             'defaultFont' => 'DejaVu Sans',
//         ]);
        
//         // Test PDF generation
//         $pdfContent = $pdf->output();
//         Log::info('PDF Content Length: ' . strlen($pdfContent));
        
//         if (empty($pdfContent)) {
//             throw new \Exception('PDF content is empty after generation');
//         }
        
//         // Return as downloadable PDF
//         return response()->streamDownload(function() use ($pdfContent) {
//             echo $pdfContent;
//         }, 'debug-tickets-report.pdf', [
//             'Content-Type' => 'application/pdf',
//             'Content-Disposition' => 'attachment; filename="debug-tickets-report.pdf"',
//             'Content-Length' => strlen($pdfContent),
//         ]);
        
//     } catch (\Exception $e) {
//         Log::error('Debug PDF Error: ' . $e->getMessage());
//         Log::error('Debug PDF Stack: ' . $e->getTraceAsString());
        
//         return response('PDF Error: ' . $e->getMessage() . "\n\nStack Trace:\n" . $e->getTraceAsString())
//             ->header('Content-Type', 'text/plain');
//     }
// });

require __DIR__.'/auth.php';