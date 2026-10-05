<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', function () {
    $user = auth()->user();

    if (! $user) {
        return redirect()->route('login');
    }

    return $user->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('pasien.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('admin', function () {
        abort_unless(auth()->user()?->isAdmin(), 403);

        return view('admin-dashboard');
    })->name('admin.dashboard');

    Route::get('pasien', function () {
        abort_if(auth()->user()?->isAdmin(), 403);

        return view('pasien-dashboard');
    })->name('pasien.dashboard');

    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
