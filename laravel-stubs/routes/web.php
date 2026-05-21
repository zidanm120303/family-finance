<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));
Route::view('/login', 'pages.auth.login')->name('login');
Route::view('/register-family', 'pages.auth.register-family')->name('register-family');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('transactions', TransactionController::class);
    Route::view('/categories', 'pages.categories.index')->name('categories.index');
    Route::view('/budgets', 'pages.budgets.index')->name('budgets.index');
    Route::view('/wallets', 'pages.wallets.index')->name('wallets.index');
    Route::view('/family-members', 'pages.family.members')->name('family.members');
    Route::view('/reports-history', 'pages.reports.history')->name('reports.history');
});
