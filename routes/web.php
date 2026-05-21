<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\RegisterFamilyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register-family', [RegisterFamilyController::class, 'show'])->name('register-family');
    Route::post('/register-family', [RegisterFamilyController::class, 'store'])->name('register-family.store');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('transactions', TransactionController::class);
    Route::post('/categories/import-default', [CategoryController::class, 'importDefaults'])->name('categories.import-default');
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('budgets', BudgetController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('wallets', WalletController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/family-members', [FamilyMemberController::class, 'index'])->name('family.members');
    Route::post('/family-members', [FamilyMemberController::class, 'store'])->name('family.members.store');
    Route::patch('/family-members/{user}', [FamilyMemberController::class, 'update'])->name('family.members.update');
    Route::get('/reports-history', [ReportController::class, 'index'])->name('reports.history');
    Route::get('/reports-history/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    Route::get('/reports-history/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
    Route::view('/settings', 'pages.settings.index')->name('settings.index');
});
