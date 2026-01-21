<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\NodeController;
use App\Http\Controllers\Admin\AdminServerController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\ServerMigrationController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\Support\TicketController;


Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role;

        return match($role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'client'  => redirect()->route('servers.index'),
            'support' => redirect()->route('home'),
            default   => redirect()->route('servers.index'),
        };
    }

    return view('welcome');
})->name('home');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('plans', SubscriptionPlanController::class);
        Route::resource('systems', SystemController::class);
        Route::resource('nodes', NodeController::class);

        Route::get('servers', [AdminServerController::class, 'index'])->name('servers.index');

        Route::get('servers/{server}/migrate', [ServerMigrationController::class, 'create'])->name('servers.migrate.form');
        Route::post('servers/{server}/migrate', [ServerMigrationController::class, 'migrate'])->name('servers.migrate');

        Route::get('servers/{server}/plan', [AdminServerController::class, 'editPlan'])->name('servers.edit_plan');
        Route::patch('servers/{server}/plan', [AdminServerController::class, 'updatePlan'])->name('servers.update_plan');

        Route::delete('servers/{server}', [AdminServerController::class, 'destroy'])->name('servers.destroy');

        Route::post('plans/{plan}/systems', [SubscriptionPlanController::class, 'updateSystems'])->name('plans.updateSystems');

        Route::get('/logs', [AuditLogController::class, 'index'])->name('logs.index');

        Route::get('/support', [SupportController::class, 'index'])->name('support.index');
        Route::post('/support/{ticket}/close', [SupportController::class, 'close'])->name('support.close');
});

Route::middleware(['auth', 'role:client'])
    ->group(function () {
        
        Route::resource('servers', ServerController::class);

        Route::post('/servers/{server}/start', [ServerController::class, 'start'])->name('servers.start');
        Route::post('/servers/{server}/stop', [ServerController::class, 'stop'])->name('servers.stop');
        Route::post('/servers/{server}/restart', [ServerController::class, 'restart'])->name('servers.restart');
        Route::get('/servers/{server}/reinstall', [ServerController::class, 'reinstall'])->name('servers.reinstall');
        Route::post('/servers/{server}/reinstall', [ServerController::class, 'postReinstall'])->name('servers.postReinstall');

        Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
        Route::post('/finance/deposit', [FinanceController::class, 'deposit'])->name('finance.deposit');

        Route::get('/support', [TicketController::class, 'index'])->name('support.index');
        Route::get('/support/create', [TicketController::class, 'create'])->name('support.create');
        Route::post('/support', [TicketController::class, 'store'])->name('support.store');
    });

Route::middleware(['auth'])->group(function () {
    Route::get('/support/{ticket}', [TicketController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/message', [TicketController::class, 'sendMessage'])->name('support.message');    
});

require __DIR__.'/auth.php';