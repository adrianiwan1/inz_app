<?php

use App\Livewire\UserActivityCharts;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;

Route::get('/', function () {
    return view('welcome');  // Strona główna, dostępna dla każdego
})->name('welcome');

Route::middleware(['auth:sanctum'])->group(function () {
    // Pracownik ma dostęp tylko do strony dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');  // Strona dashboard
    })->name('dashboard');

    Route::get('/user-activity', UserActivityCharts::class)->name('user-activity');
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Admin ma dostęp do wszystkich stron
    Route::get('/admin-panel', function () {
        return view('adminPanel.index');  // Strona dla admina
    })->name('admin.panel');


//    Route::get('/some-other-admin-page', function () {
//        return view('adminPanel.other');  // Strona dla admina
//    })->name('admin.other');
});
