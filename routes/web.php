<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('users/create', [RegisteredUserController::class, 'create'])->name('users.create');
    Route::post('users', [RegisteredUserController::class, 'store'])->name('users.store');
    Route::resource('users', Controllers\UsersController::class)->except(['create', 'store']);
    Route::resource('roles', Controllers\RoleController::class);
    Route::resource('permissions', Controllers\PermissionController::class);
});

require __DIR__ . '/settings.php';
