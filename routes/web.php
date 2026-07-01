<?php

use App\Http\Controllers\NotificationController;

    

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/mood', [DashboardController::class, 'saveMood'])->name('dashboard.mood');
    Route::post('/dashboard/note', [DashboardController::class, 'saveNote'])->name('dashboard.note');
    Route::post('/dashboard/ai', [DashboardController::class, 'askAI'])->name('dashboard.ai');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::resource('transactions', App\Http\Controllers\TransactionController::class);
    Route::resource('schedules', App\Http\Controllers\ScheduleController::class);
    Route::resource('goals', App\Http\Controllers\GoalController::class);

    Route::middleware('auth')->group(function () {
    Route::get('/notifications/pending', [NotificationController::class, 'pending'])
        ->name('notifications.pending');

});
});
