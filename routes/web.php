<?php

use App\Livewire\B2B;
use App\Livewire\UserActivityCharts;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;

Route::get('/', function () {
    return redirect()->route('login');  // Strona główna, dostępna dla każdego
})->name('welcome');

//Route::get('/', function () {
//    return view('welcome');  // Strona główna, dostępna dla każdego
//})->name('welcome');

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

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/b2b', B2B::class)->name('b2b');
});

//Route::get('/b2b', B2B::class)->middleware(['auth:sanctum'])->name('b2b');
//Route::middleware(['auth:sanctum'])->get('/profile/b2b-settings', \App\Http\Livewire\ProfileB2BSettings::class)->name('profile.b2b-settings');

