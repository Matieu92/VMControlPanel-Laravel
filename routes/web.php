<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\SubscriptionPlanController;
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
        
    });

Route::middleware(['auth', 'role:client'])
    ->group(function () {
        
        Route::resource('servers', ServerController::class);
    
    });

require __DIR__.'/auth.php';