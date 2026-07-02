<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AiController;

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/mood', [DashboardController::class, 'saveMood'])->name('dashboard.mood');
    Route::post('/dashboard/note', [DashboardController::class, 'saveNote'])->name('dashboard.note');
    Route::post('/dashboard/ai', [DashboardController::class, 'askAI'])->name('dashboard.ai');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Resources
    Route::resource('transactions', TransactionController::class);
    Route::resource('schedules', ScheduleController::class);
    Route::resource('goals', GoalController::class);

    // Notifikasi
    Route::get('/notifications/pending', [NotificationController::class, 'pending'])->name('notifications.pending');

    // AI Chat
    Route::post('/ai/chat', [AiController::class, 'chat'])->name('ai.chat');
});