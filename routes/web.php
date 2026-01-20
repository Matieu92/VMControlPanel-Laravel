<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\NodeController;
use App\Http\Controllers\Admin\AdminServerController;
use App\Http\Controllers\Admin\ServerMigrationController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\Support\TicketController;


Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role;

        return match($role) {
            'admin'   => redirect()->route('admin.plans.index'),
            'client'  => redirect()->route('servers.index'),
            'support' => redirect()->route('dashboard'),
            default   => redirect()->route('servers.index'),
        };
    }

    return view('welcome');
});

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        Route::resource('plans', SubscriptionPlanController::class);

        Route::resource('systems', SystemController::class);

        Route::resource('nodes', NodeController::class);

        Route::get('servers', [AdminServerController::class, 'index'])->name('servers.index');

        Route::get('servers/{server}/migrate', [ServerMigrationController::class, 'create'])->name('servers.migrate.form');
        Route::post('servers/{server}/migrate', [ServerMigrationController::class, 'migrate'])->name('servers.migrate');
    });

Route::middleware(['auth', 'role:client'])
    ->group(function () {
        
        Route::resource('servers', ServerController::class);

        Route::post('/servers/{server}/start', [ServerController::class, 'start'])->name('servers.start');
        Route::post('/servers/{server}/stop', [ServerController::class, 'stop'])->name('servers.stop');
        Route::post('/servers/{server}/restart', [ServerController::class, 'restart'])->name('servers.restart');
    
    });

require __DIR__.'/auth.php';